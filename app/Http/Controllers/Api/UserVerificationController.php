<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
        
        // Check if user has email verification token (pending verification)
        $hasPendingVerification = \App\Models\VerificationToken::where('user_id', $user->id)->exists();
        
        // Create response data
        $verificationData = [
            'user_type' => $userType,
            'user_id' => $user->id,
            'email' => $user->email,
            'email_verified' => $user->hasVerifiedEmail(),
            'email_token' => $hasPendingVerification ? 'pending' : null,
            'whatsapp_verified' => false, // For now, we'll keep these as false
            'whatsapp_number' => null,
            'whatsapp_code' => null,
            'linkedin_verified' => false,
            'linkedin_id' => null,
            'profile_completed' => false,
        ];
        
        Log::info('Verification status retrieved', [
            'user_type' => $userType,
            'user_id' => $user->id,
            'email_verified' => $verificationData['email_verified'],
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

    // ارسال کد واتساپ (ساده شده)
    public function sendWhatsappCode(Request $request, $userType, $userId)
    {
        $request->validate([
            'whatsapp_number' => 'required|string',
        ]);

        // For now, just return success without actually sending WhatsApp code
        Log::info('WhatsApp verification requested', [
            'user_type' => $userType,
            'user_id' => $userId,
            'number' => $request->input('whatsapp_number')
        ]);

        return response()->json(['success' => true, 'message' => 'WhatsApp verification feature coming soon']);
    }

    // تایید کد واتساپ (ساده شده)
    public function verifyWhatsappCode(Request $request, $userType, $userId)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // For now, just return success
        Log::info('WhatsApp code verification requested', [
            'user_type' => $userType,
            'user_id' => $userId,
            'code' => $request->input('code')
        ]);

        return response()->json(['success' => true, 'message' => 'WhatsApp verification feature coming soon']);
    }

    // اتصال لینکدین (ساده شده)
    public function connectLinkedin(Request $request, $userType, $userId)
    {
        Log::info('LinkedIn connection requested', [
            'user_type' => $userType,
            'user_id' => $userId
        ]);

        return response()->json(['success' => true, 'message' => 'LinkedIn connection feature coming soon']);
    }

    // تکمیل پروفایل
    public function completeProfile(Request $request, $userType, $userId)
    {
        try {
            // Find the user
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            // Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Please verify your email address before completing your profile.'
                ], 400);
            }

            Log::info('Profile completed successfully', [
                'user_type' => $userType,
                'user_id' => $userId,
                'email_verified' => $user->hasVerifiedEmail()
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

    // سایر متدها برای سازگاری
    public function startLinkedinOAuth(Request $request, $userType, $userId)
    {
        return response()->json(['success' => true, 'message' => 'LinkedIn OAuth feature coming soon']);
    }

    public function linkedinCallback(Request $request)
    {
        return redirect('/registration/timeline/individual/1?linkedin=success');
    }

    public function verifyLinkedinCode(Request $request, $userType, $userId)
    {
        return response()->json(['success' => true, 'message' => 'LinkedIn verification feature coming soon']);
    }
} 