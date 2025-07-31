<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserVerification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // بالای فایل اضافه شود

class UserVerificationController extends Controller
{
    // دریافت وضعیت وریفای
    public function show($userType, $userId)
    {
        $verification = UserVerification::where('user_type', $userType)
            ->where('user_id', $userId)
            ->first();
        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Verification not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $verification]);
    }

    // ارسال ایمیل وریفای
    public function sendEmailVerification(Request $request, $userType, $userId)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        $verification = UserVerification::firstOrCreate([
            'user_type' => $userType,
            'user_id' => $userId,
        ]);
        
        $token = Str::random(32);
        $verification->email_token = $token;
        $verification->email_verified = false;
        $verification->email = $request->input('email');
        $verification->save();

        // Create verification URL with redirect
        $redirectUrl = $request->input('redirect_url', config('app.frontend_url') . '/login');
        $verificationUrl = config('app.frontend_url') . '/verification/email/verify/' . $token . '?redirect=' . urlencode($redirectUrl);

        // Send email using custom template
        $email = $request->input('email');
        $name = $request->input('name');
        
        $data = [
            'subject' => 'Verify Your Email Address - ' . config('app.name'),
            'title' => 'Verify Your Email Address',
            'body' => "Hey {$name}<br><br>
                      Please verify your email address to complete your profile verification. 
                      Click the button below to verify your email:",
            'actionUrl' => $verificationUrl,
            'actionText' => 'Verify Email',
            'importantNote' => "This link is valid for 24 hours. If you didn't request this, please ignore this email."
        ];

        Mail::send('emails.base-template', $data, function ($message) use ($email, $data) {
            $message->to($email)->subject($data['subject']);
        });

        return response()->json(['success' => true, 'message' => 'Verification email sent']);
    }

    // تایید ایمیل
    public function verifyEmail($token)
    {
        $verification = UserVerification::where('email_token', $token)->first();
        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 404);
        }
        
        $verification->email_verified = true;
        $verification->email_token = null;
        $verification->save();
        
        // Check if there's a redirect URL in the query parameters
        $redirectUrl = request()->query('redirect');
        if ($redirectUrl) {
            // Add verified=true parameter to the redirect URL
            $separator = strpos($redirectUrl, '?') !== false ? '&' : '?';
            $redirectUrl .= $separator . 'verified=true';
            return redirect($redirectUrl);
        }
        
        // Default redirect to login if no redirect URL provided
        return redirect(config('app.frontend_url') . '/login?verified=true');
    }

    // ارسال کد واتساپ
    public function sendWhatsappCode(Request $request, $userType, $userId)
    {
        $request->validate([
            'whatsapp_number' => 'required|string',
        ]);

        try {
            $verification = \App\Models\UserVerification::firstOrCreate([
                'user_type' => $userType,
                'user_id' => $userId,
            ]);

            $code = rand(100000, 999999);
            $whatsappNumber = $request->input('whatsapp_number');
            
            // Clean phone number (remove + and spaces)
            $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
            
            $verification->whatsapp_number = $whatsappNumber;
            $verification->whatsapp_code = $code;
            $verification->whatsapp_verified = false;
            $verification->save();

            // ارسال پیام با WhatsApp Cloud API
            $accessToken = config('services.whatsapp.access_token');
            $phoneNumberId = config('services.whatsapp.phone_number_id');

            if (!$accessToken || !$phoneNumberId) {
                Log::error('WhatsApp configuration missing', [
                    'access_token' => $accessToken ? 'set' : 'missing',
                    'phone_number_id' => $phoneNumberId ? 'set' : 'missing'
                ]);
                return response()->json(['success' => false, 'message' => 'WhatsApp service not configured'], 500);
            }

            Log::info('Sending WhatsApp code', [
                'user_type' => $userType,
                'user_id' => $userId,
                'number' => $whatsappNumber,
                'code' => $code
            ]);

            // شماره باید با کد کشور و بدون + باشد (مثلاً: 98912xxxxxxx)
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                    "messaging_product" => "whatsapp",
                    "to" => $whatsappNumber,
                    "type" => "text",
                    "text" => [
                        "body" => "کد تایید شما در Explorer Elite: {$code}"
                    ]
                ]);

            if ($response->failed()) {
                Log::error('WhatsApp API error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'number' => $whatsappNumber
                ]);
                return response()->json(['success' => false, 'message' => 'Failed to send WhatsApp code. Please check your number.'], 500);
            }

            Log::info('WhatsApp code sent successfully', [
                'user_type' => $userType,
                'user_id' => $userId,
                'number' => $whatsappNumber
            ]);

            return response()->json(['success' => true, 'message' => 'WhatsApp code sent successfully']);

        } catch (\Exception $e) {
            Log::error('WhatsApp verification error', [
                'error' => $e->getMessage(),
                'user_type' => $userType,
                'user_id' => $userId
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred while sending WhatsApp code'], 500);
        }
    }

    // تایید کد واتساپ
    public function verifyWhatsappCode(Request $request, $userType, $userId)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        try {
            $verification = UserVerification::where('user_type', $userType)
                ->where('user_id', $userId)
                ->first();

            if (!$verification) {
                return response()->json(['success' => false, 'message' => 'Verification not found'], 404);
            }

            if (!$verification->whatsapp_code) {
                return response()->json(['success' => false, 'message' => 'No verification code found. Please request a new code.'], 400);
            }

            if ($verification->whatsapp_verified) {
                return response()->json(['success' => true, 'message' => 'WhatsApp already verified']);
            }

            if ($verification->whatsapp_code !== $request->input('code')) {
                Log::warning('Invalid WhatsApp code attempt', [
                    'user_type' => $userType,
                    'user_id' => $userId,
                    'provided_code' => $request->input('code'),
                    'expected_code' => $verification->whatsapp_code
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid verification code'], 400);
            }

            $verification->whatsapp_verified = true;
            $verification->whatsapp_code = null; // Clear the code after successful verification
            $verification->save();

            Log::info('WhatsApp verification successful', [
                'user_type' => $userType,
                'user_id' => $userId,
                'number' => $verification->whatsapp_number
            ]);

            return response()->json(['success' => true, 'message' => 'WhatsApp verified successfully']);

        } catch (\Exception $e) {
            Log::error('WhatsApp verification error', [
                'error' => $e->getMessage(),
                'user_type' => $userType,
                'user_id' => $userId
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred while verifying WhatsApp code'], 500);
        }
    }

    // اتصال لینکدین (OAuth callback)
    public function connectLinkedin(Request $request, $userType, $userId)
    {
        $verification = UserVerification::firstOrCreate([
            'user_type' => $userType,
            'user_id' => $userId,
        ]);
        $verification->linkedin_verified = true;
        $verification->linkedin_id = $request->input('linkedin_id');
        $verification->save();
        return response()->json(['success' => true, 'message' => 'LinkedIn connected']);
    }

    // تکمیل پروفایل
    public function completeProfile(Request $request, $userType, $userId)
    {
        try {
            $verification = UserVerification::firstOrCreate([
                'user_type' => $userType,
                'user_id' => $userId,
            ]);

            // Check if at least one verification is completed
            $hasVerification = $verification->email_verified || 
                              $verification->whatsapp_verified || 
                              $verification->linkedin_verified;

            if (!$hasVerification) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Please complete at least one verification step before proceeding.'
                ], 400);
            }

            $verification->profile_completed = true;
            $verification->save();

            Log::info('Profile completed successfully', [
                'user_type' => $userType,
                'user_id' => $userId,
                'email_verified' => $verification->email_verified,
                'whatsapp_verified' => $verification->whatsapp_verified,
                'linkedin_verified' => $verification->linkedin_verified
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Profile completed successfully. You can now access the dashboard.'
            ]);

        } catch (\Exception $e) {
            Log::error('Profile completion error', [
                'error' => $e->getMessage(),
                'user_type' => $userType,
                'user_id' => $userId
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'An error occurred while completing profile'
            ], 500);
        }
    }

    // شروع OAuth لینکدین
    public function startLinkedinOAuth(Request $request, $userType, $userId)
    {
        try {
            $clientId = config('services.linkedin.client_id');
            $redirectUri = config('services.linkedin.redirect');
            
            if (!$clientId || !$redirectUri) {
                Log::error('LinkedIn OAuth configuration missing', [
                    'client_id' => $clientId ? 'set' : 'missing',
                    'redirect_uri' => $redirectUri ? 'set' : 'missing'
                ]);
                return redirect('/registration/timeline/' . $userType . '/' . $userId . '?linkedin=error');
            }
            
            $state = Str::random(32);
            
            // Ensure session is started and store data
            if (!session()->isStarted()) {
                session()->start();
            }
            
            session([
                'linkedin_oauth_state' => $state,
                'linkedin_user_type' => $userType,
                'linkedin_user_id' => $userId
            ]);
            
            // Force session save
            session()->save();
            
            // Use OpenID Connect with openid scope
            $scopes = config('services.linkedin.scopes', 'openid');
            $url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id={$clientId}&redirect_uri=" . urlencode($redirectUri) . "&state={$state}&scope=" . urlencode($scopes);
            
            Log::info('Starting LinkedIn OAuth (OpenID Connect)', [
                'user_type' => $userType,
                'user_id' => $userId,
                'state' => $state,
                'redirect_uri' => $redirectUri,
                'scopes' => $scopes,
                'url' => $url,
                'session_id' => session()->getId(),
                'session_state_stored' => session('linkedin_oauth_state')
            ]);
            
            return redirect($url);
        } catch (\Exception $e) {
            Log::error('LinkedIn OAuth start error', [
                'error' => $e->getMessage(),
                'user_type' => $userType,
                'user_id' => $userId
            ]);
            return redirect('/registration/timeline/' . $userType . '/' . $userId . '?linkedin=error');
        }
    }

    // Callback لینکدین
    public function linkedinCallback(Request $request)
    {
        try {
            // Ensure session is started
            if (!session()->isStarted()) {
                session()->start();
            }
            
            $state = $request->get('state');
            $code = $request->get('code');
            $error = $request->get('error');
            
            Log::info('LinkedIn callback received', [
                'state' => $state,
                'has_code' => !empty($code),
                'error' => $error,
                'session_state' => session('linkedin_oauth_state'),
                'session_user_type' => session('linkedin_user_type'),
                'session_user_id' => session('linkedin_user_id'),
                'session_id' => session()->getId()
            ]);
            
            if ($error) {
                Log::error('LinkedIn OAuth error', ['error' => $error]);
                return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
            }
            
            // Check if session data exists
            if (!session('linkedin_oauth_state') || !session('linkedin_user_type') || !session('linkedin_user_id')) {
                Log::error('LinkedIn OAuth session data missing', [
                    'session_state' => session('linkedin_oauth_state'),
                    'session_user_type' => session('linkedin_user_type'),
                    'session_user_id' => session('linkedin_user_id')
                ]);
                return redirect('/registration/timeline/individual/1?linkedin=error');
            }
            
            if ($state !== session('linkedin_oauth_state')) {
                Log::error('LinkedIn OAuth state mismatch', [
                    'received_state' => $state,
                    'session_state' => session('linkedin_oauth_state')
                ]);
                return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
            }

            if (!$code) {
                Log::error('LinkedIn OAuth no code received');
                return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
            }

            $clientId = config('services.linkedin.client_id');
            $clientSecret = config('services.linkedin.client_secret');
            $redirectUri = config('services.linkedin.redirect');
            
            // Get access token
            $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);
            
            if ($response->failed()) {
                Log::error('LinkedIn access token request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
            }
            
            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'] ?? null;
            
            if (!$accessToken) {
                Log::error('LinkedIn access token not received', ['response' => $tokenData]);
                return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
            }

            // Get user info from OpenID Connect userinfo endpoint
            $profileResponse = Http::withToken($accessToken)->get('https://api.linkedin.com/v2/userinfo');
            
            if ($profileResponse->failed()) {
                Log::error('LinkedIn userinfo request failed', [
                    'status' => $profileResponse->status(),
                    'body' => $profileResponse->body(),
                    'headers' => $profileResponse->headers()
                ]);
                
                // Try alternative endpoint if userinfo fails
                $profileResponse = Http::withToken($accessToken)->get('https://api.linkedin.com/v2/me');
                if ($profileResponse->failed()) {
                    Log::error('LinkedIn profile request also failed', [
                        'status' => $profileResponse->status(),
                        'body' => $profileResponse->body()
                    ]);
                    return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
                }
            }
            
            $profile = $profileResponse->json();
            $linkedinId = $profile['sub'] ?? $profile['id'] ?? null; // Try both OpenID Connect and regular OAuth

            if (!$linkedinId) {
                Log::error('LinkedIn user ID not found', ['profile' => $profile]);
                return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
            }

            $verification = \App\Models\UserVerification::where('user_type', session('linkedin_user_type'))
                ->where('user_id', session('linkedin_user_id'))->first();
                
            if ($verification) {
                $verification->linkedin_verified = true;
                $verification->linkedin_id = $linkedinId;
                $verification->save();
                
                Log::info('LinkedIn verification successful (OpenID Connect)', [
                    'user_type' => session('linkedin_user_type'),
                    'user_id' => session('linkedin_user_id'),
                    'linkedin_id' => $linkedinId,
                    'profile' => $profile
                ]);
            } else {
                Log::error('LinkedIn verification record not found', [
                    'user_type' => session('linkedin_user_type'),
                    'user_id' => session('linkedin_user_id')
                ]);
            }

            // Store user data before clearing session
            $userType = session('linkedin_user_type');
            $userId = session('linkedin_user_id');

            // Clear session data after successful verification
            session()->forget(['linkedin_oauth_state', 'linkedin_user_type', 'linkedin_user_id']);

            return redirect("/registration/timeline/{$userType}/{$userId}?linkedin=success");
        } catch (\Exception $e) {
            Log::error('LinkedIn callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/registration/timeline/individual/1?linkedin=error');
        }
    }

    // تایید کد لینکدین (برای استفاده در صورت نیاز)
    public function verifyLinkedinCode(Request $request, $userType, $userId)
    {
        $verification = UserVerification::where('user_type', $userType)
            ->where('user_id', $userId)
            ->first();
        
        if (!$verification) {
            return response()->json(['success' => false, 'message' => 'Verification not found'], 404);
        }

        // Since LinkedIn OAuth is handled through redirect flow, this method is mainly for API consistency
        // The actual verification happens in linkedinCallback method
        if ($verification->linkedin_verified) {
            return response()->json(['success' => true, 'message' => 'LinkedIn already verified']);
        }

        return response()->json(['success' => false, 'message' => 'LinkedIn verification required through OAuth flow']);
    }
} 
