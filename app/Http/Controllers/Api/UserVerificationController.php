<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserVerification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
        $verification = UserVerification::firstOrCreate([
            'user_type' => $userType,
            'user_id' => $userId,
        ]);
        $token = Str::random(32);
        $verification->email_token = $token;
        $verification->email_verified = false;
        $verification->save();

        // ارسال ایمیل (قالب ساده، بعداً می‌توان سفارشی کرد)
        $email = $request->input('email');
        Mail::send('emails.verify', ['token' => $token, 'name' => $request->input('name')], function ($message) use ($email) {
            $message->to($email)->subject('Verify Your Email Address');
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
        return response()->json(['success' => true, 'message' => 'Email verified']);
    }

    // ارسال کد واتساپ
    public function sendWhatsappCode(Request $request, $userType, $userId)
    {
        $verification = UserVerification::firstOrCreate([
            'user_type' => $userType,
            'user_id' => $userId,
        ]);
        $code = rand(100000, 999999);
        $verification->whatsapp_number = $request->input('whatsapp_number');
        $verification->whatsapp_code = $code;
        $verification->whatsapp_verified = false;
        $verification->save();

        // اینجا باید API رسمی واتساپ را صدا بزنید (نمونه شبیه‌سازی)
        Log::info('Send WhatsApp code', ['number' => $verification->whatsapp_number, 'code' => $code]);

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
} 
