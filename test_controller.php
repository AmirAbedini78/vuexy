<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Api\EmailVerificationController;

echo "Testing controller directly...\n\n";

$token = 'awvuRvxdCEDrpMKAag12KA70KvimSd9lA1uzcwpBiOnRrEWlfP4icCj4wxlIkA4U';

try {
    $controller = new EmailVerificationController();
    $response = $controller->verifyByToken($token);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} 
