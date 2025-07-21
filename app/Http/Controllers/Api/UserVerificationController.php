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
            return redirect($redirectUrl);
        }
        
        return response()->json(['success' => true, 'message' => 'Email verified']);
    }

    // ارسال کد واتساپ
    public function sendWhatsappCode(Request $request, $userType, $userId)
    {
        $request->validate([
            'whatsapp_number' => 'required|string',
        ]);
        $verification = \App\Models\UserVerification::firstOrCreate([
            'user_type' => $userType,
            'user_id' => $userId,
        ]);
        $code = rand(100000, 999999);
        $verification->whatsapp_number = $request->input('whatsapp_number');
        $verification->whatsapp_code = $code;
        $verification->whatsapp_verified = false;
        $verification->save();

        // ارسال پیام با WhatsApp Cloud API
        $accessToken = config('services.whatsapp.access_token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $to = $verification->whatsapp_number;

        // شماره باید با کد کشور و بدون + باشد (مثلاً: 98912xxxxxxx)
        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                "messaging_product" => "whatsapp",
                "to" => $to,
                "type" => "text",
                "text" => [
                    "body" => "کد تایید شما در Explorer Elite: {$code}"
                ]
            ]);

        // اگر می‌خواهید از template واتس‌اپ استفاده کنید (اختیاری):
        // $templateName = config('services.whatsapp.template_name');
        // $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
        //     ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
        //         "messaging_product" => "whatsapp",
        //         "to" => $to,
        //         "type" => "template",
        //         "template" => [
        //             "name" => $templateName,
        //             "language" => ["code" => "fa"],
        //             "components" => [
        //                 [
        //                     "type" => "body",
        //                     "parameters" => [
        //                         ["type" => "text", "text" => $code]
        //                     ]
        //                 ]
        //             ]
        //         ]
        //     ]);

        if ($response->failed()) {
            \Log::error('WhatsApp API error', ['response' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Failed to send WhatsApp code.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'WhatsApp code sent']);
    }

    // تایید کد واتساپ
    public function verifyWhatsappCode(Request $request, $userType, $userId)
    {
        $verification = UserVerification::where('user_type', $userType)
            ->where('user_id', $userId)
            ->first();
        if (!$verification || $verification->whatsapp_code !== $request->input('code')) {
            return response()->json(['success' => false, 'message' => 'Invalid code'], 400);
        }
        $verification->whatsapp_verified = true;
        $verification->whatsapp_code = null;
        $verification->save();
        return response()->json(['success' => true, 'message' => 'WhatsApp verified']);
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
        $verification = UserVerification::firstOrCreate([
            'user_type' => $userType,
            'user_id' => $userId,
        ]);
        $verification->profile_completed = true;
        $verification->save();
        return response()->json(['success' => true, 'message' => 'Profile completed']);
    }

    // شروع OAuth لینکدین
    public function startLinkedinOAuth(Request $request, $userType, $userId)
    {
        $clientId = config('services.linkedin.client_id');
        $redirectUri = urlencode(config('services.linkedin.redirect'));
        $state = Str::random(32);
        session([
            'linkedin_oauth_state' => $state,
            'linkedin_user_type' => $userType,
            'linkedin_user_id' => $userId
        ]);
        $scope = 'r_liteprofile r_emailaddress';

        $url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&state={$state}&scope={$scope}";
        return redirect($url);
    }

    // Callback لینکدین
    public function linkedinCallback(Request $request)
    {
        $state = $request->get('state');
        $code = $request->get('code');
        if ($state !== session('linkedin_oauth_state')) {
            return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
        }

        $clientId = config('services.linkedin.client_id');
        $clientSecret = config('services.linkedin.client_secret');
        $redirectUri = config('services.linkedin.redirect');
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
        $accessToken = $response->json()['access_token'] ?? null;
        if (!$accessToken) {
            return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=error');
        }

        $profile = Http::withToken($accessToken)->get('https://api.linkedin.com/v2/me')->json();
        $linkedinId = $profile['id'] ?? null;

        $verification = \App\Models\UserVerification::where('user_type', session('linkedin_user_type'))
            ->where('user_id', session('linkedin_user_id'))->first();
        if ($verification && $linkedinId) {
            $verification->linkedin_verified = true;
            $verification->linkedin_id = $linkedinId;
            $verification->save();
        }

        return redirect('/registration/timeline/' . session('linkedin_user_type') . '/' . session('linkedin_user_id') . '?linkedin=success');
    }
} 
