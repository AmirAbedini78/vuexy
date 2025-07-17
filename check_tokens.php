<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\VerificationToken;

echo "Current time: " . now() . "\n";
echo "Timezone: " . config('app.timezone') . "\n\n";

$tokens = VerificationToken::with('user')->get();

if ($tokens->isEmpty()) {
    echo "No verification tokens found in database.\n";
} else {
    echo "Found " . $tokens->count() . " verification token(s):\n\n";
    
    foreach ($tokens as $token) {
        echo "Token: " . $token->token . "\n";
        echo "User: " . $token->user->email . "\n";
        echo "Created: " . $token->created_at . "\n";
        echo "Expires: " . $token->expires_at . "\n";
        echo "Is Expired: " . ($token->isExpired() ? 'YES' : 'NO') . "\n";
        echo "---\n";
    }
} 
