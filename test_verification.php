<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\VerificationToken;
use Illuminate\Support\Str;

echo "=== EMAIL VERIFICATION TEST ===\n";

// Check if we have any users
$users = User::all();
echo "Total users: " . $users->count() . "\n";

if ($users->count() > 0) {
    $user = $users->first();
    echo "First user: {$user->email}\n";
    echo "Email verified: " . ($user->hasVerifiedEmail() ? 'Yes' : 'No') . "\n";
    
    // Check existing tokens
    $tokens = VerificationToken::where('user_id', $user->id)->get();
    echo "Existing tokens: " . $tokens->count() . "\n";
    
    if ($tokens->count() > 0) {
        foreach ($tokens as $token) {
            echo "Token: {$token->token}\n";
            echo "Expires at: {$token->expires_at->toDateTimeString()}\n";
            echo "Is expired: " . ($token->isExpired() ? 'Yes' : 'No') . "\n";
            echo "Current time: " . now()->toDateTimeString() . "\n";
            echo "Timezone: " . config('app.timezone') . "\n";
            echo "---\n";
        }
    }
    
    // Create a new token for testing
    echo "\nCreating new test token...\n";
    $user->sendEmailVerificationNotification();
    
    $newTokens = VerificationToken::where('user_id', $user->id)->get();
    echo "New tokens: " . $newTokens->count() . "\n";
    
    if ($newTokens->count() > 0) {
        $newToken = $newTokens->first();
        echo "New token: {$newToken->token}\n";
        echo "Expires at: {$newToken->expires_at->toDateTimeString()}\n";
        echo "Is expired: " . ($newToken->isExpired() ? 'Yes' : 'No' : 'No') . "\n";
        
        // Test the API endpoint
        $apiUrl = config('app.url') . "/api/verify/{$newToken->token}";
        echo "API URL: {$apiUrl}\n";
        
        // Make a test request
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
        
        echo "HTTP Status: {$httpCode}\n";
        echo "Response: {$response}\n";
    }
} else {
    echo "No users found in database.\n";
}

echo "\n=== TEST COMPLETE ===\n"; 
