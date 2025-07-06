<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verifyByToken($token)
    {
        \Log::info('Verifying token', [
            'token' => $token,
            'current_time' => now()->toDateTimeString(),
        ]);

        $record = VerificationToken::where('token', $token)->first();

        if (!$record) {
            \Log::warning('Verification token not found', ['token' => $token]);
            return response()->json(['message' => 'لینک تأیید نامعتبر است یا یافت نشد'], 400);
        }

        \Log::info('Token found', [
            'token' => $token,
            'expires_at' => $record->expires_at ? $record->expires_at->toDateTimeString() : null,
            'current_time' => now()->toDateTimeString(),
        ]);

        if ($record->expires_at && $record->expires_at->isPast()) {
            \Log::warning('Verification token expired', [
                'token' => $token,
                'expires_at' => $record->expires_at->toDateTimeString(),
                'current_time' => now()->toDateTimeString(),
            ]);
            $record->delete();
            return response()->json(['message' => 'لینک تأیید منقضی شده است'], 400);
        }

        $user = $record->user;
        if (!$user) {
            \Log::warning('User not found for token', ['token' => $token]);
            return response()->json(['message' => 'کاربر یافت نشد'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            $record->delete();
            \Log::info('Email already verified', ['user_id' => $user->id]);
            return response()->json(['message' => 'ایمیل قبلاً تأیید شده است'], 200);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));
        $record->delete();
        \Log::info('Email verified successfully', [
            'user_id' => $user->id,
            'email_verified_at' => $user->email_verified_at->toDateTimeString(),
        ]);

        return response()->json(['message' => 'ایمیل با موفقیت تأیید شد'], 200);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'ایمیل قبلاً تأیید شده است'], 400);
        }

        $request->user()->sendEmailVerificationNotification();
        \Log::info('Verification email resent', ['user_id' => $request->user()->id]);

        return response()->json(['message' => 'لینک تأیید جدید به ایمیل شما ارسال شد']);
    }
}