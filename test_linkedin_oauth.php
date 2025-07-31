<?php

// Simple test to verify LinkedIn OAuth configuration
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Config;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "LinkedIn OAuth Configuration Test\n";
echo "================================\n";

echo "Client ID: " . config('services.linkedin.client_id') . "\n";
echo "Client Secret: " . (config('services.linkedin.client_secret') ? 'SET' : 'NOT SET') . "\n";
echo "Redirect URI: " . config('services.linkedin.redirect') . "\n";

echo "\nTesting OAuth URL generation:\n";
$clientId = config('services.linkedin.client_id');
$redirectUri = config('services.linkedin.redirect');
$state = 'test_state_123';
$scope = 'r_liteprofile r_emailaddress';

$url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id={$clientId}&redirect_uri=" . urlencode($redirectUri) . "&state={$state}&scope={$scope}";

echo "Generated URL: " . $url . "\n";

echo "\nTest completed!\n"; 
