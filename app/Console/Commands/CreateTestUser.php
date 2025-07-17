<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    protected $signature = 'create:test-user {email} {name?}';
    protected $description = 'Create a test user and send verification email';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name') ?? 'Test User';

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->warn("User with email {$email} already exists!");
            $this->info("Sending verification email to existing user...");
            $existingUser->sendEmailVerificationNotification();
            $this->info("Verification email sent to {$email}");
            
            // Get the verification token for existing user
            $token = \App\Models\VerificationToken::where('user_id', $existingUser->id)->first();
            if ($token) {
                $this->info("Verification token: {$token->token}");
                $this->info("Frontend URL: http://vuexy.test/verify/{$token->token}");
                $this->info("API URL: http://vuexy.test/api/verify/{$token->token}");
            } else {
                $this->error("No verification token found!");
            }
            return 0;
        }

        // Create new user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $this->info("User created successfully: {$user->email}");

        // Send verification email
        $user->sendEmailVerificationNotification();
        $this->info("Verification email sent to {$email}");

        // Get the verification token
        $token = \App\Models\VerificationToken::where('user_id', $user->id)->first();
        if ($token) {
            $this->info("Verification token: {$token->token}");
            $this->info("Frontend URL: http://vuexy.test/verify/{$token->token}");
            $this->info("API URL: http://vuexy.test/api/verify/{$token->token}");
        } else {
            $this->error("No verification token found!");
        }

        return 0;
    }
} 
