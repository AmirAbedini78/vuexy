<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use App\Http\Controllers\Api\EmailVerificationController;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role
        ]);
    
        event(new Registered($user));
        
        // Send verification email
        $user->sendEmailVerificationNotification();
    
        return response()->json([
            'message' => 'User registered successfully. Please check your email for a verification link.',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        \Log::info('Login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);
    
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
    
            if (!$user->hasVerifiedEmail()) {
                \Log::warning('Login failed: Email not verified', ['email' => $request->email]);
                return response()->json([
                    'message' => 'Your email address is not verified.',
                    'errors' => [
                        'email' => ['Please verify your email address before logging in.']
                    ]
                ], 403);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
            
            \Log::info('Login successful', ['user_id' => $user->id]);
    
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
    
        \Log::warning('Login failed: Invalid credentials', ['email' => $request->email]);
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    /**
     * Send a password reset link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['errors' => ['email' => [__($status)]]], 422);
    }

    /**
     * Reset the password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['errors' => ['email' => [__($status)]]], 422);
    }

    /**
     * 2FA: Setup
     */
    public function setupTwoFactor(Request $request)
    {
        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();
        $user->google2fa_secret = $secret;
        $user->save();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        return response()->json([
            'message' => '2FA setup initiated. Scan the QR code with your authenticator app.',
            'qr_code_url' => $qrCodeUrl,
            'secret' => $secret,
        ]);
    }

    /**
     * 2FA: Verify
     */
    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['one_time_password' => 'required']);
        $user = $request->user();
        $authenticator = app(Authenticator::class)->boot($request);
        if ($authenticator->verifyGoogle2FA($user->google2fa_secret, $request->one_time_password)) {
            $user->save();
            $token = $user->createToken('auth_token', ['2fa_verified'])->plainTextToken;
            return response()->json([
                'message' => '2FA verified successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
        return response()->json(['errors' => ['one_time_password' => ['Invalid code']]], 422);
    }

    /**
     * 2FA: Disable
     */
    public function disableTwoFactor(Request $request)
    {
        $user = $request->user();
        $user->google2fa_secret = null;
        $user->save();
        return response()->json(['message' => '2FA disabled successfully']);
    }
}