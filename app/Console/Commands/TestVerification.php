<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Console\Command;

class TestVerification extends Command
{
    protected $signature = 'test:verification';
    protected $description = 'Test email verification system';

    public function handle()
    {
        $this->info('=== EMAIL VERIFICATION TEST ===');

        // Check if we have any users
        $users = User::all();
        $this->info("Total users: " . $users->count());

        if ($users->count() > 0) {
            $user = $users->first();
            $this->info("First user: {$user->email}");
            $this->info("Email verified: " . ($user->hasVerifiedEmail() ? 'Yes' : 'No'));
            
            // Check existing tokens
            $tokens = VerificationToken::where('user_id', $user->id)->get();
            $this->info("Existing tokens: " . $tokens->count());
            
            if ($tokens->count() > 0) {
                foreach ($tokens as $token) {
                    $this->info("Token: {$token->token}");
                    $this->info("Expires at: {$token->expires_at->toDateTimeString()}");
                    $this->info("Is expired: " . ($token->isExpired() ? 'Yes' : 'No'));
                    $this->info("Current time: " . now()->toDateTimeString());
                    $this->info("Timezone: " . config('app.timezone'));
                    $this->info("---");
                }
            }
            
            // Create a new token for testing
            $this->info("\nCreating new test token...");
            $user->sendEmailVerificationNotification();
            
            $newTokens = VerificationToken::where('user_id', $user->id)->get();
            $this->info("New tokens: " . $newTokens->count());
            
            if ($newTokens->count() > 0) {
                $newToken = $newTokens->first();
                $this->info("New token: {$newToken->token}");
                $this->info("Expires at: {$newToken->expires_at->toDateTimeString()}");
                $this->info("Is expired: " . ($newToken->isExpired() ? 'Yes' : 'No'));
                
                // Test the API endpoint
                $apiUrl = config('app.url') . "/api/verify/{$newToken->token}";
                $this->info("API URL: {$apiUrl}");
                
                // Make a test request using Laravel's HTTP client
                try {
                    $response = \Illuminate\Support\Facades\Http::get($apiUrl);
                    $this->info("HTTP Status: " . $response->status());
                    $this->info("Response: " . $response->body());
                } catch (\Exception $e) {
                    $this->error("Error: " . $e->getMessage());
                }
            }
        } else {
            $this->error("No users found in database.");
        }

        $this->info("\n=== TEST COMPLETE ===");
        return 0;
    }
} 
