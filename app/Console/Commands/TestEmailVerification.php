<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestEmailVerification extends Command
{
    protected $signature = 'test:email-verification {email?}';
    protected $description = 'Test email verification token creation and expiration';

    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            $user = User::where('email', $email)->first();
        } else {
            $user = User::first();
        }

        if (!$user) {
            $this->error('No user found');
            return 1;
        }

        $this->info("Testing email verification for user: {$user->email}");
        $this->info("Current time: " . now()->toDateTimeString());
        $this->info("Timezone: " . config('app.timezone'));

        // Clean up existing tokens
        VerificationToken::where('user_id', $user->id)->delete();

        // Create a test token
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(config('auth.verification.expire', 1440));

        $this->info("Creating token with expiration: {$expiresAt->toDateTimeString()}");

        $verificationToken = VerificationToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        $this->info("Token created successfully!");
        $this->info("Token: {$token}");
        $this->info("Expires at: {$verificationToken->expires_at->toDateTimeString()}");
        $this->info("Is expired: " . ($verificationToken->isExpired() ? 'Yes' : 'No'));

        // Test the verification endpoint
        $url = config('app.frontend_url') . "/verify/{$token}";
        $this->info("Frontend URL: {$url}");

        $apiUrl = config('app.url') . "/api/verify/{$token}";
        $this->info("API URL: {$apiUrl}");

        // Test the API endpoint
        $this->info("\nTesting API endpoint...");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->info("HTTP Status: {$httpCode}");
        $this->info("Response: {$response}");

        // Clean up
        $verificationToken->delete();
        $this->info("\nTest token cleaned up.");

        return 0;
    }
} 
