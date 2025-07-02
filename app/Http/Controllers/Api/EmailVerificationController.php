<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\VerificationToken;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        $user = User::find($request->route('id'));

        if (!$user) {
            return response()->json(['message' => 'Invalid user ID'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        if (hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            return response()->json(['message' => 'Email verified successfully'], 200);
        }

        return response()->json(['message' => 'Invalid verification link'], 400);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'A fresh verification link has been sent to your email address.']);
    }

    /**
     * Verify user by token
     */
    public function verifyByToken($token)
    {
        $record = \App\Models\VerificationToken::where('token', $token)->first();
        \Log::info('Verify token debug', [
            'token' => $token,
            'record' => $record,
            'now' => now(),
            'expires_at' => $record?->expires_at,
        ]);
        if (!$record) {
            return response()->json(['message' => 'Invalid or expired verification link'], 400);
        }
        if ($record->expires_at && $record->expires_at->isPast()) {
            return response()->json(['message' => 'Invalid or expired verification link'], 400);
        }
        $user = $record->user;
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->hasVerifiedEmail()) {
            $record->delete();
            return response()->json(['message' => 'Email already verified'], 200);
        }
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
        $record->delete();
        return response()->json(['message' => 'Email verified successfully'], 200);
    }
}
