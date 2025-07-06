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
        \Log::info('Received verification request', [
            'token' => $token,
            'current_time' => now()->toDateTimeString(),
            'request_headers' => request()->headers->all(),
        ]);

        $record = VerificationToken::where('token', $token)->first();

        if (!$record) {
            \Log::warning('Verification token not found', ['token' => $token]);
            return response()->json(['message' => 'The verification link is invalid or not found'], 400);
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
            return response()->json(['message' => 'The verification link has expired'], 400);
        }

        $user = $record->user;
        if (!$user) {
            \Log::warning('User not found for token', ['token' => $token]);
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            $record->delete();
            \Log::info('Email already verified', ['user_id' => $user->id]);
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));
        $record->delete();
        \Log::info('Email verified successfully', [
            'user_id' => $user->id,
            'email_verified_at' => $user->email_verified_at->toDateTimeString(),
        ]);

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $request->user()->sendEmailVerificationNotification();
        \Log::info('Verification email resent', ['user_id' => $request->user()->id]);

        return response()->json(['message' => 'A new verification link has been sent to your email']);
    }
}