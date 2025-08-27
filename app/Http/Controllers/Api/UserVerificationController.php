<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class UserVerificationController extends Controller
{
    // دریافت وضعیت وریفای
    public function show($userType, $userId)
    {
        Log::info('Fetching verification status', [
            'user_type' => $userType,
            'user_id' => $userId
        ]);
        
        // Find the user
        $user = User::find($userId);
        if (!$user) {
            Log::info('User not found', [
                'user_type' => $userType,
                'user_id' => $userId
            ]);
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        
        // Get or create verification record
        $verification = UserVerification::firstOrCreate(
            ['user_type' => $userType, 'user_id' => $user->id],
            [
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'whatsapp_verified' => false,
                'linkedin_verified' => false,
                'profile_completed' => false,
                'status' => 'pending'
            ]
        );
        
        // Check if user has email verification token (pending verification)
        $hasPendingVerification = \App\Models\VerificationToken::where('user_id', $user->id)->exists();
        
        // Update email verification status
        $verification->email_verified = $user->hasVerifiedEmail();
        $verification->email_token = $hasPendingVerification ? 'pending' : null;
        $verification->save();
        
        // Create response data
        $verificationData = [
            'user_type' => $userType,
            'user_id' => $user->id,
            'email' => $user->email,
            'email_verified' => $user->hasVerifiedEmail(),
            'email_token' => $hasPendingVerification ? 'pending' : null,
            'whatsapp_verified' => $verification->whatsapp_verified,
            'whatsapp_number' => $verification->whatsapp_number,
            'whatsapp_code' => $verification->whatsapp_code ? 'sent' : null,
            'linkedin_verified' => $verification->linkedin_verified,
            'linkedin_id' => $verification->linkedin_id,
            'profile_completed' => $verification->profile_completed,
        ];
        
        Log::info('Verification status retrieved', [
            'user_type' => $userType,
            'user_id' => $user->id,
            'email_verified' => $verificationData['email_verified'],
            'whatsapp_verified' => $verificationData['whatsapp_verified'],
            'linkedin_verified' => $verificationData['linkedin_verified'],
            'has_pending_verification' => $hasPendingVerification,
            'email_verified_at' => $user->email_verified_at
        ]);
        
        return response()->json(['success' => true, 'data' => $verificationData]);
    }

    // دریافت وضعیت وریفای برای کاربر فعلی (بدون وابستگی به individual/company)
    public function showForCurrentUser($userId)
    {
        Log::info('Fetching verification status for current user', [
            'user_id' => $userId
        ]);
        
        // Find the user
        $user = User::find($userId);
        if (!$user) {
            Log::info('User not found', [
                'user_id' => $userId
            ]);
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        
        // Get or create verification record with default user_type
        $verification = UserVerification::firstOrCreate(
            ['user_id' => $user->id],
            [
                'user_type' => 'user', // Default type
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'whatsapp_verified' => false,
                'linkedin_verified' => false,
                'profile_completed' => false,
                'status' => 'pending'
            ]
        );
        
        // Check if user has email verification token (pending verification)
        $hasPendingVerification = \App\Models\VerificationToken::where('user_id', $user->id)->exists();
        
        // Update email verification status
        $verification->email_verified = $user->hasVerifiedEmail();
        $verification->email_token = $hasPendingVerification ? 'pending' : null;
        $verification->save();
        
        // Create response data
        $verificationData = [
            'user_id' => $user->id,
            'email' => $user->email,
            'email_verified' => $user->hasVerifiedEmail(),
            'email_token' => $hasPendingVerification ? 'pending' : null,
            'whatsapp_verified' => $verification->whatsapp_verified,
            'whatsapp_number' => $verification->whatsapp_number,
            'whatsapp_code' => $verification->whatsapp_code ? 'sent' : null,
            'linkedin_verified' => $verification->linkedin_verified,
            'linkedin_id' => $verification->linkedin_id,
            'profile_completed' => $verification->profile_completed,
        ];
        
        Log::info('Verification status retrieved for current user', [
            'user_id' => $user->id,
            'email_verified' => $verificationData['email_verified'],
            'whatsapp_verified' => $verificationData['whatsapp_verified'],
            'linkedin_verified' => $verificationData['linkedin_verified'],
            'has_pending_verification' => $hasPendingVerification,
            'email_verified_at' => $user->email_verified_at
        ]);
        
        return response()->json(['success' => true, 'data' => $verificationData]);
    }

    // ارسال ایمیل وریفای
    public function sendEmailVerification(Request $request, $userType, $userId)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Use the same system as main registration
        // Delete any existing verification tokens for this user
        \App\Models\VerificationToken::where('user_id', $user->id)->delete();
        
        // Create new verification token
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(60);
        
        \App\Models\VerificationToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Create verification URL with redirect
        $redirectUrl = $request->input('redirect_url', config('app.frontend_url') . '/login');
        $verificationUrl = config('app.frontend_url') . '/verify/' . $token . '?redirect=' . urlencode($redirectUrl);

        // Send email using the same notification system as main registration
        $user->notify(new \App\Notifications\VerifyEmail($verificationUrl));

        Log::info('Timeline email verification sent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'expires_at' => $expiresAt->toDateTimeString(),
            'redirect_url' => $redirectUrl
        ]);

        return response()->json(['success' => true, 'message' => 'Verification email sent']);
    }

    // ارسال ایمیل وریفای برای کاربر فعلی
    public function sendEmailVerificationForCurrentUser(Request $request, $userId)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Use the same system as main registration
        // Delete any existing verification tokens for this user
        \App\Models\VerificationToken::where('user_id', $user->id)->delete();
        
        // Create new verification token
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(60);
        
        \App\Models\VerificationToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Create verification URL with redirect
        $redirectUrl = $request->input('redirect_url', config('app.frontend_url') . '/timeline');
        $verificationUrl = config('app.frontend_url') . '/verify/' . $token . '?redirect=' . urlencode($redirectUrl);

        // Send email using the same notification system as main registration
        $user->notify(new \App\Notifications\VerifyEmail($verificationUrl));

        Log::info('Timeline email verification sent for current user', [
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'expires_at' => $expiresAt->toDateTimeString(),
            'redirect_url' => $redirectUrl
        ]);

        return response()->json(['success' => true, 'message' => 'Verification email sent']);
    }

    // ارسال کد واتساپ با استفاده از WhatsApp Business API
    public function sendWhatsappCode(Request $request, $userType, $userId)
    {
        $request->validate([
            'whatsapp_number' => 'required|string|regex:/^[0-9]+$/',
        ]);

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get or create verification record
        $verification = UserVerification::firstOrCreate(
            ['user_type' => $userType, 'user_id' => $user->id],
            [
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'whatsapp_verified' => false,
                'linkedin_verified' => false,
                'profile_completed' => false,
                'status' => 'pending'
            ]
        );

        // Update WhatsApp number
        $verification->whatsapp_number = $request->whatsapp_number;
        $verification->save();

        // Generate verification code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store code in verification record
        $verification->whatsapp_code = $code;
        $verification->save();

        // Format phone number for WhatsApp API
        $phoneNumber = $request->whatsapp_number;
        if (!str_starts_with($phoneNumber, '+')) {
            $phoneNumber = '+98' . $phoneNumber; // Assuming Iran (+98)
        }

        // Send WhatsApp message using WhatsApp Business API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.whatsapp.token'),
                'Content-Type' => 'application/json',
            ])->post(config('services.whatsapp.api_url') . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $phoneNumber,
                'type' => 'template',
                'template' => [
                    'name' => 'verification_code',
                    'language' => [
                        'code' => 'en'
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $code
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp verification code sent successfully', [
                    'user_type' => $userType,
                    'user_id' => $userId,
                    'phone_number' => $phoneNumber,
                    'code' => $code,
                    'response' => $response->json()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Verification code sent to WhatsApp'
                ]);
            } else {
                Log::error('WhatsApp API error', [
                    'user_type' => $userType,
                    'user_id' => $userId,
                    'phone_number' => $phoneNumber,
                    'response' => $response->json()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send WhatsApp code'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp verification error', [
                'user_type' => $userType,
                'user_id' => $userId,
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'WhatsApp service unavailable'
            ], 500);
        }
    }

    // ارسال کد واتساپ برای کاربر فعلی
    public function sendWhatsappCodeForCurrentUser(Request $request, $userId)
    {
        $request->validate([
            'whatsapp_number' => 'required|string|regex:/^[0-9]+$/',
        ]);

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get or create verification record
        $verification = UserVerification::firstOrCreate(
            ['user_id' => $user->id],
            [
                'user_type' => 'user',
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'whatsapp_verified' => false,
                'linkedin_verified' => false,
                'profile_completed' => false,
                'status' => 'pending'
            ]
        );

        // Update WhatsApp number
        $verification->whatsapp_number = $request->whatsapp_number;
        $verification->save();

        // Generate verification code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store code in verification record
        $verification->whatsapp_code = $code;
        $verification->save();

        // Format phone number for WhatsApp API
        $phoneNumber = $request->whatsapp_number;
        if (!str_starts_with($phoneNumber, '+')) {
            $phoneNumber = '+98' . $phoneNumber; // Assuming Iran (+98)
        }

        // Send WhatsApp message using WhatsApp Business API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.whatsapp.token'),
                'Content-Type' => 'application/json',
            ])->post(config('services.whatsapp.api_url') . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $phoneNumber,
                'type' => 'template',
                'template' => [
                    'name' => 'verification_code',
                    'language' => [
                        'code' => 'en'
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $code
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp verification code sent successfully for current user', [
                    'user_id' => $userId,
                    'phone_number' => $phoneNumber,
                    'code' => $code,
                    'response' => $response->json()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Verification code sent to WhatsApp'
                ]);
            } else {
                Log::error('WhatsApp API error for current user', [
                    'user_id' => $userId,
                    'phone_number' => $phoneNumber,
                    'response' => $response->json()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send WhatsApp code'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp verification error for current user', [
                'user_id' => $userId,
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'WhatsApp service unavailable'
            ], 500);
        }
    }

    // تایید کد واتساپ
    public function verifyWhatsappCode(Request $request, $userType, $userId)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get verification record
        $verification = UserVerification::where('user_type', $userType)
            ->where('user_id', $user->id)
            ->first();

        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Verification record not found'], 404);
        }

        // Check if code matches
        if ($verification->whatsapp_code !== $request->code) {
            return response()->json(['success' => false, 'message' => 'Invalid verification code'], 400);
        }

        // Mark WhatsApp as verified
        $verification->whatsapp_verified = true;
        $verification->whatsapp_code = null; // Clear the code
        $verification->save();

        Log::info('WhatsApp verification completed', [
            'user_type' => $userType,
            'user_id' => $user->id,
            'whatsapp_number' => $verification->whatsapp_number
        ]);

        return response()->json(['success' => true, 'message' => 'WhatsApp verification completed']);
    }

    // تایید کد واتساپ برای کاربر فعلی
    public function verifyWhatsappCodeForCurrentUser(Request $request, $userId)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get verification record
        $verification = UserVerification::where('user_id', $user->id)->first();

        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Verification record not found'], 404);
        }

        // Check if code matches
        if ($verification->whatsapp_code !== $request->code) {
            return response()->json(['success' => false, 'message' => 'Invalid verification code'], 400);
        }

        // Mark WhatsApp as verified
        $verification->whatsapp_verified = true;
        $verification->whatsapp_code = null; // Clear the code
        $verification->save();

        Log::info('WhatsApp verification completed for current user', [
            'user_id' => $user->id,
            'whatsapp_number' => $verification->whatsapp_number
        ]);

        return response()->json(['success' => true, 'message' => 'WhatsApp verification completed']);
    }

    // شروع فرآیند تایید لینکدین
    public function startLinkedinOAuth(Request $request, $userType, $userId)
    {
        try {
            Log::info('Starting LinkedIn OAuth', [
                'user_type' => $userType,
                'user_id' => $userId,
                'request_url' => $request->fullUrl()
            ]);

            // Find the user
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            // Store user info in session for callback
            session(['linkedin_verification' => [
                'user_type' => $userType,
                'user_id' => $userId,
                'user_email' => $user->email
            ]]);

            // Generate LinkedIn OAuth URL
            $authUrl = Socialite::driver('linkedin')
                ->redirect()
                ->getTargetUrl();

            Log::info('Generated LinkedIn OAuth URL', [
                'auth_url' => $authUrl,
                'user_type' => $userType,
                'user_id' => $userId
            ]);

            // Return JSON response with auth URL for frontend to redirect
            return response()->json([
                'success' => true,
                'authorization_url' => $authUrl,
                'message' => 'Redirecting to LinkedIn for authentication'
            ]);

        } catch (\Exception $e) {
            Log::error('LinkedIn OAuth start error', [
                'error' => $e->getMessage(),
                'user_type' => $userType,
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize LinkedIn authentication. Please try again.'
            ], 500);
        }
    }

    // شروع فرآیند تایید لینکدین برای کاربر فعلی
    public function startLinkedinOAuthForCurrentUser(Request $request, $userId)
    {
        try {
            Log::info('Starting LinkedIn OAuth for current user', [
                'user_id' => $userId,
                'request_url' => $request->fullUrl()
            ]);

            // Find the user
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            // Get redirect URL from request or use default
            $redirectUrl = $request->input('redirect_url', config('app.frontend_url') . '/timeline');

            // Store user info in session for callback
            session(['linkedin_verification_current_user' => [
                'user_id' => $userId,
                'user_email' => $user->email,
                'redirect_url' => $redirectUrl
            ]]);

            // Generate LinkedIn OAuth URL with redirect parameter
            $callbackUrl = config('app.url') . '/build/callback/linkedin?redirect=' . urlencode($redirectUrl);
            $authUrl = Socialite::driver('linkedin')
                ->redirectUrl($callbackUrl)
                ->redirect()
                ->getTargetUrl();

            Log::info('Generated LinkedIn OAuth URL for current user', [
                'auth_url' => $authUrl,
                'user_id' => $userId,
                'redirect_url' => $redirectUrl
            ]);

            // Return JSON response with auth URL for frontend to redirect
            return response()->json([
                'success' => true,
                'authorization_url' => $authUrl,
                'message' => 'Redirecting to LinkedIn for authentication'
            ]);

        } catch (\Exception $e) {
            Log::error('LinkedIn OAuth start error for current user', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize LinkedIn authentication. Please try again.'
            ], 500);
        }
    }

    // تایید بازگشتی لینکدین
    public function linkedinCallback(Request $request)
    {
        try {
            Log::info('LinkedIn callback received', [
                'query_params' => $request->all(),
                'session_data' => session('linkedin_verification'),
                'current_user_session' => session('linkedin_verification_current_user')
            ]);

            // Check if this is a current user verification
            $currentUserVerificationData = session('linkedin_verification_current_user');
            if ($currentUserVerificationData) {
                Log::info('Current user LinkedIn verification detected, redirecting to current user handler');
                return $this->linkedinCallbackForCurrentUser($request);
            }

            // Get user info from session for legacy system
            $verificationData = session('linkedin_verification');
            if (!$verificationData) {
                Log::error('No LinkedIn verification session data found');
                return redirect('/timeline?linkedin=error&message=session_expired');
            }

            $userType = $verificationData['user_type'];
            $userId = $verificationData['user_id'];

            // Handle OAuth errors
            if ($request->has('error')) {
                Log::error('LinkedIn OAuth error', [
                    'error' => $request->get('error'),
                    'error_description' => $request->get('error_description'),
                    'user_type' => $userType,
                    'user_id' => $userId
                ]);

                return redirect("/timeline?linkedin=error&message=oauth_denied");
            }

            // Get user data from LinkedIn
            $linkedinUser = Socialite::driver('linkedin')->user();
            
            Log::info('LinkedIn user data received', [
                'linkedin_id' => $linkedinUser->getId(),
                'linkedin_email' => $linkedinUser->getEmail(),
                'linkedin_name' => $linkedinUser->getName(),
                'user_type' => $userType,
                'user_id' => $userId
            ]);

            // Find our user
            $user = User::find($userId);
            if (!$user) {
                Log::error('User not found during LinkedIn callback', ['user_id' => $userId]);
                return redirect("/timeline?linkedin=error&message=user_not_found");
            }

            // Create or update verification record
            $verification = UserVerification::firstOrCreate([
                'user_type' => $userType,
                'user_id' => $userId,
            ], [
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'whatsapp_verified' => false,
                'linkedin_verified' => false,
                'profile_completed' => false,
                'status' => 'pending'
            ]);

            // Update LinkedIn verification data
            $verification->linkedin_verified = true;
            $verification->linkedin_id = $linkedinUser->getId();
            $verification->linkedin_email = $linkedinUser->getEmail();
            $verification->linkedin_name = $linkedinUser->getName();
            $verification->linkedin_avatar = $linkedinUser->getAvatar();
            $verification->save();

            // Clear session data
            session()->forget('linkedin_verification');

            Log::info('LinkedIn verification completed successfully', [
                'user_type' => $userType,
                'user_id' => $userId,
                'linkedin_id' => $linkedinUser->getId(),
                'linkedin_email' => $linkedinUser->getEmail()
            ]);

            return redirect("/timeline?linkedin=success");

        } catch (\Exception $e) {
            Log::error('LinkedIn callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Try to get user info from session for redirect
            $verificationData = session('linkedin_verification');
            $userType = $verificationData['user_type'] ?? 'individual';
            $userId = $verificationData['user_id'] ?? '1';

            session()->forget('linkedin_verification');
            
            return redirect("/timeline?linkedin=error&message=callback_error");
        }
    }

    // تایید بازگشتی لینکدین برای کاربر فعلی
    public function linkedinCallbackForCurrentUser(Request $request)
    {
        try {
            Log::info('LinkedIn callback received for current user', [
                'query_params' => $request->all(),
                'session_data' => session('linkedin_verification_current_user')
            ]);

            // Get user info from session
            $verificationData = session('linkedin_verification_current_user');
            if (!$verificationData) {
                Log::error('No LinkedIn verification session data found for current user');
                return redirect('/timeline?linkedin=error&message=session_expired');
            }

            $userId = $verificationData['user_id'];

            // Handle OAuth errors
            if ($request->has('error')) {
                Log::error('LinkedIn OAuth error for current user', [
                    'error' => $request->get('error'),
                    'error_description' => $request->get('error_description'),
                    'user_id' => $userId
                ]);

                return redirect("/timeline?linkedin=error&message=oauth_denied");
            }

            // Get user data from LinkedIn
            $linkedinUser = Socialite::driver('linkedin')->user();
            
            Log::info('LinkedIn user data received for current user', [
                'linkedin_id' => $linkedinUser->getId(),
                'linkedin_email' => $linkedinUser->getEmail(),
                'linkedin_name' => $linkedinUser->getName(),
                'user_id' => $userId
            ]);

            // Find our user
            $user = User::find($userId);
            if (!$user) {
                Log::error('User not found during LinkedIn callback for current user', ['user_id' => $userId]);
                return redirect("/timeline?linkedin=error&message=user_not_found");
            }

            // Create or update verification record
            $verification = UserVerification::firstOrCreate([
                'user_id' => $userId,
            ], [
                'user_type' => 'user',
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'whatsapp_verified' => false,
                'linkedin_verified' => false,
                'profile_completed' => false,
                'status' => 'pending'
            ]);

            // Update LinkedIn verification data
            $verification->linkedin_verified = true;
            $verification->linkedin_id = $linkedinUser->getId();
            $verification->linkedin_email = $linkedinUser->getEmail();
            $verification->linkedin_name = $linkedinUser->getName();
            $verification->linkedin_avatar = $linkedinUser->getAvatar();
            $verification->save();

            // Get redirect URL from session
            $verificationData = session('linkedin_verification_current_user');
            $redirectUrl = $verificationData['redirect_url'] ?? null;

            // Clear session data
            session()->forget('linkedin_verification_current_user');

            Log::info('LinkedIn verification completed successfully for current user', [
                'user_id' => $userId,
                'linkedin_id' => $linkedinUser->getId(),
                'linkedin_email' => $linkedinUser->getEmail(),
                'verification_record_updated' => true,
                'redirect_url' => $redirectUrl
            ]);

            // Use redirect URL from session or fallback to default
            if ($redirectUrl) {
                // Add linkedin=success parameter to the redirect URL
                $separator = strpos($redirectUrl, '?') !== false ? '&' : '?';
                $redirectUrl .= $separator . 'linkedin=success';
                
                Log::info('Redirecting to custom URL with LinkedIn success parameter', ['redirect_url' => $redirectUrl]);
                return redirect($redirectUrl);
            }

            return redirect("/timeline?linkedin=success");

        } catch (\Exception $e) {
            Log::error('LinkedIn callback error for current user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Try to get user info from session for redirect
            $verificationData = session('linkedin_verification_current_user');
            $userId = $verificationData['user_id'] ?? '1';

            session()->forget('linkedin_verification_current_user');
            
            return redirect("/timeline?linkedin=error&message=callback_error");
        }
    }

    // تکمیل پروفایل
    public function completeProfile(Request $request, $userType, $userId)
    {
        $request->validate([
            'completed_verifications' => 'required|integer|min:0',
            'total_verifications' => 'required|integer|min:1',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get or create verification record
        $verification = UserVerification::firstOrCreate(
            ['user_type' => $userType, 'user_id' => $user->id],
            [
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'whatsapp_verified' => false,
                'linkedin_verified' => false,
                'profile_completed' => false,
                'status' => 'pending'
            ]
        );

        // Mark profile as completed
        $verification->profile_completed = true;
        $verification->status = 'completed';
        $verification->save();

        Log::info('Profile completion', [
            'user_type' => $userType,
            'user_id' => $user->id,
            'completed_verifications' => $request->completed_verifications,
            'total_verifications' => $request->total_verifications,
            'progress_percentage' => $request->progress_percentage
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile completed successfully',
            'redirect_url' => '/access-control'
        ]);
    }

    // تکمیل پروفایل برای کاربر فعلی
    public function completeProfileForCurrentUser(Request $request, $userId)
    {
        $request->validate([
            'completed_verifications' => 'required|integer|min:0',
            'total_verifications' => 'required|integer|min:1',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get or create verification record
        $verification = UserVerification::firstOrCreate(
            ['user_id' => $user->id],
            [
                'user_type' => 'user',
                'email' => $user->email,
                'email_verified' => $user->hasVerifiedEmail(),
                'whatsapp_verified' => false,
                'linkedin_verified' => false,
                'profile_completed' => false,
                'status' => 'pending'
            ]
        );

        // Mark profile as completed
        $verification->profile_completed = true;
        $verification->status = 'completed';
        $verification->save();

        Log::info('Profile completion for current user', [
            'user_id' => $user->id,
            'completed_verifications' => $request->completed_verifications,
            'total_verifications' => $request->total_verifications,
            'progress_percentage' => $request->progress_percentage
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile completed successfully',
            'redirect_url' => '/access-control'
        ]);
    }
} 
