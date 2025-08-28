<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    public function verifyByToken($token)
    {
        Log::info('=== EMAIL VERIFICATION START ===');
        Log::info('Received verification request', [
            'token' => $token,
            'current_time' => now()->toDateTimeString(),
            'current_timezone' => config('app.timezone'),
            'request_headers' => request()->headers->all(),
        ]);

        // Clean up expired tokens first
        $this->cleanupExpiredTokens();
        
        $record = VerificationToken::where('token', $token)->first();

        if (!$record) {
            Log::warning('Verification token not found', ['token' => $token]);
            Log::info('=== EMAIL VERIFICATION END (TOKEN NOT FOUND) ===');
            return redirect()->route('login')->withQueryString(['type' => 'error', 'message' => 'The verification link is invalid or not found']);
        }

        Log::info('Token found', [
            'token' => $token,
            'expires_at' => $record->expires_at ? $record->expires_at->toDateTimeString() : null,
            'expires_at_timezone' => $record->expires_at ? $record->expires_at->timezone->getName() : null,
            'current_time' => now()->toDateTimeString(),
            'current_timezone' => config('app.timezone'),
            'is_past' => $record->expires_at ? $record->expires_at->isPast() : false,
            'is_expired_custom' => $record->isExpired(),
            'time_difference_minutes' => $record->expires_at ? now()->diffInMinutes($record->expires_at, false) : null,
        ]);

        if ($record->isExpired()) {
            Log::warning('Verification token expired', [
                'token' => $token,
                'expires_at' => $record->expires_at->toDateTimeString(),
                'current_time' => now()->toDateTimeString(),
                'time_difference_minutes' => now()->diffInMinutes($record->expires_at, false),
            ]);
            $record->delete();
            Log::info('=== EMAIL VERIFICATION END (TOKEN EXPIRED) ===');
            return redirect()->route('login')->withQueryString(['type' => 'error', 'message' => 'The verification link has expired']);
        }

        $user = $record->user;
        if (!$user) {
            Log::warning('User not found for token', ['token' => $token]);
            Log::info('=== EMAIL VERIFICATION END (USER NOT FOUND) ===');
            return redirect()->route('login')->withQueryString(['type' => 'error', 'message' => 'User not found']);
        }

        if ($user->hasVerifiedEmail()) {
            $record->delete();
            Log::info('Email already verified', ['user_id' => $user->id]);
            Log::info('=== EMAIL VERIFICATION END (ALREADY VERIFIED) ===');
            return redirect()->route('login')->withQueryString(['type' => 'success', 'message' => 'Email already verified']);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));
        $record->delete();
        Log::info('Email verified successfully', [
            'user_id' => $user->id,
            'email_verified_at' => $user->email_verified_at->toDateTimeString(),
        ]);
        Log::info('=== EMAIL VERIFICATION END (SUCCESS) ===');

        // Check if there's a redirect URL in the query parameters
        $redirectUrl = request()->query('redirect');
        Log::info('Email verification redirect check', [
            'has_redirect' => !empty($redirectUrl),
            'redirect_url' => $redirectUrl,
            'all_query_params' => request()->query()
        ]);
        
        if ($redirectUrl) {
            // Decode the redirect URL if it's encoded
            $decodedRedirectUrl = urldecode($redirectUrl);
            Log::info('Processing redirect URL', [
                'original' => $redirectUrl,
                'decoded' => $decodedRedirectUrl
            ]);
            
            // Add verified=true parameter to the redirect URL
            $separator = strpos($decodedRedirectUrl, '?') !== false ? '&' : '?';
            $finalRedirectUrl = $decodedRedirectUrl . $separator . 'verified=true';
            
            Log::info('Final redirect URL', ['final_url' => $finalRedirectUrl]);
            return redirect($finalRedirectUrl);
        }

        // If no specific redirect URL, redirect to the proper timeline route with user context
        $userType = $user->role === 'user' ? 'individual' : ($user->role === 'company' ? 'company' : 'individual');
        $timelineUrl = "/registration/timeline/{$userType}/{$user->id}?verified=true";
        Log::info('Redirecting to timeline with user context', [
            'user_id' => $user->id,
            'user_type' => $userType,
            'timeline_url' => $timelineUrl
        ]);
        return redirect($timelineUrl);
    }

    private function cleanupExpiredTokens()
    {
        $expiredTokens = VerificationToken::where('expires_at', '<', now())->get();
        foreach ($expiredTokens as $token) {
            $token->delete();
        }
        if ($expiredTokens->count() > 0) {
            Log::info('Cleaned up expired tokens', ['count' => $expiredTokens->count()]);
        }
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $request->user()->sendEmailVerificationNotification();
        Log::info('Verification email resent', ['user_id' => $request->user()->id]);

        return response()->json(['message' => 'A new verification link has been sent to your email']);
    }

    public function resendWithoutAuth(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $user->sendEmailVerificationNotification();
        Log::info('Verification email resent without auth', ['user_id' => $user->id, 'email' => $request->email]);

        return response()->json(['message' => 'A new verification link has been sent to your email']);
    }
}
