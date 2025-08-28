<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\UserVerificationController;

// Test route to check if web routes are working
Route::get('/web-test', function () {
    return 'Web route is working!';
});

// Email verification route
Route::get('/verify/{token}', [EmailVerificationController::class, 'verifyByToken']);

// Password reset route
Route::get('/reset-password/{token}', function ($token) {
    return view('application');
})->name('password.reset');

// Login route (for redirects)
Route::get('/login', function () {
    return view('application');
})->name('login');

// LinkedIn OAuth routes
Route::get('/api/verification/{userType}/{userId}/linkedin', [UserVerificationController::class, 'startLinkedinOAuth']);
Route::get('/linkedin/callback', [UserVerificationController::class, 'linkedinCallback']);
Route::get('/linkedin/callback/current-user', [UserVerificationController::class, 'linkedinCallbackForCurrentUser']);

// LinkedIn callback for production (matches the LinkedIn app redirect URI)
Route::get('/build/callback/linkedin', [UserVerificationController::class, 'linkedinCallback']);

// Test LinkedIn OAuth configuration
Route::get('/test-linkedin-config', function() {
    $config = config('services.linkedin-openid');
    return response()->json([
        'client_id' => $config['client_id'] ?? 'NOT SET',
        'redirect_uri' => $config['redirect'] ?? 'NOT SET',
        'scopes' => $config['scopes'] ?? 'NOT SET',
        'env_values' => [
            'LINKEDIN_CLIENT_ID' => env('LINKEDIN_CLIENT_ID', 'NOT SET'),
            'LINKEDIN_CLIENT_SECRET' => env('LINKEDIN_CLIENT_SECRET') ? 'SET' : 'NOT SET',
            'LINKEDIN_REDIRECT_URI' => env('LINKEDIN_REDIRECT_URI', 'NOT SET'),
            'LINKEDIN_SCOPES' => env('LINKEDIN_SCOPES', 'NOT SET'),
        ]
    ]);
});

// Simple LinkedIn OAuth test
Route::get('/test-simple-linkedin/{userType}/{userId}', function($userType, $userId) {
    try {
        $controller = new \App\Http\Controllers\Api\UserVerificationController();
        return $controller->startLinkedinOAuth(request(), $userType, $userId);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Mock LinkedIn OAuth for testing (temporary)
Route::get('/mock-linkedin/{userType}/{userId}', function($userType, $userId) {
    // Simulate successful LinkedIn verification
    $verification = \App\Models\UserVerification::firstOrCreate([
        'user_type' => $userType,
        'user_id' => $userId,
    ]);
    
    $verification->linkedin_verified = true;
    $verification->linkedin_id = 'mock_linkedin_id_' . time();
    $verification->linkedin_email = 'mock@linkedin.com';
    $verification->linkedin_name = 'Mock LinkedIn User';
    $verification->linkedin_avatar = 'https://media.licdn.com/dms/image/mock-avatar.jpg';
    $verification->save();
    
    return redirect("/registration/timeline/{$userType}/{$userId}?linkedin=success");
});

// Mock LinkedIn OAuth for current user testing
Route::get('/mock-linkedin-current-user/{userId}', function($userId) {
    // Simulate successful LinkedIn verification for current user
    $verification = \App\Models\UserVerification::firstOrCreate([
        'user_id' => $userId,
    ], [
        'user_type' => 'user',
        'email' => 'test@example.com',
        'email_verified' => false,
        'whatsapp_verified' => false,
        'linkedin_verified' => false,
        'profile_completed' => false,
        'status' => 'pending'
    ]);
    
    $verification->linkedin_verified = true;
    $verification->linkedin_id = 'mock_linkedin_id_' . time();
    $verification->linkedin_email = 'mock@linkedin.com';
    $verification->linkedin_name = 'Mock LinkedIn User';
    $verification->linkedin_avatar = 'https://media.licdn.com/dms/image/mock-avatar.jpg';
    $verification->save();
    
    return redirect("/timeline?linkedin=success");
});

// Test route for LinkedIn OAuth
Route::get('/test-linkedin/{userType}/{userId}', function ($userType, $userId) {
    // Simulate successful LinkedIn verification
    $verification = \App\Models\UserVerification::firstOrCreate([
        'user_type' => $userType,
        'user_id' => $userId,
    ]);
    
    $verification->linkedin_verified = true;
    $verification->linkedin_id = 'mock_linkedin_id_' . time();
    $verification->linkedin_email = 'test@linkedin.com';
    $verification->linkedin_name = 'Test LinkedIn User';
    $verification->linkedin_avatar = 'https://media.licdn.com/dms/image/test-avatar.jpg';
    $verification->save();
    
    return redirect("/registration/timeline/{$userType}/{$userId}?linkedin=success");
});

// Test route for LinkedIn OAuth
Route::get('/test-linkedin/{userType}/{userId}', function ($userType, $userId) {
    return response()->json([
        'message' => 'LinkedIn OAuth test route working',
        'user_type' => $userType,
        'user_id' => $userId,
        'linkedin_client_id' => config('services.linkedin.client_id'),
        'linkedin_redirect_uri' => config('services.linkedin.redirect')
    ]);
});

// Direct test for LinkedIn OAuth redirect
Route::get('/test-linkedin-redirect/{userType}/{userId}', function ($userType, $userId) {
    $clientId = config('services.linkedin.client_id');
    $redirectUri = config('services.linkedin.redirect');
    $state = 'test_state_' . time();
    $scope = 'r_liteprofile r_emailaddress';

    $url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id={$clientId}&redirect_uri=" . urlencode($redirectUri) . "&state={$state}&scope={$scope}";
    
    return response()->json([
        'message' => 'LinkedIn OAuth redirect URL generated',
        'redirect_url' => $url,
        'user_type' => $userType,
        'user_id' => $userId
    ]);
});

// Direct test for LinkedIn OAuth controller method
Route::get('/test-linkedin-controller/{userType}/{userId}', [UserVerificationController::class, 'startLinkedinOAuth']);

// Comprehensive test for LinkedIn OAuth configuration
Route::get('/test-linkedin-complete/{userType}/{userId}', function ($userType, $userId) {
    $clientId = config('services.linkedin.client_id');
    $clientSecret = config('services.linkedin.client_secret');
    $redirectUri = config('services.linkedin.redirect');
    
    $state = 'test_state_' . time();
    $scope = 'r_liteprofile';
    
    $url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id={$clientId}&redirect_uri=" . urlencode($redirectUri) . "&state={$state}&scope={$scope}";
    
    return response()->json([
        'message' => 'LinkedIn OAuth complete configuration test',
        'configuration' => [
            'client_id' => $clientId,
            'client_secret' => $clientSecret ? 'SET' : 'NOT SET',
            'redirect_uri' => $redirectUri,
            'session_driver' => config('session.driver'),
            'session_lifetime' => config('session.lifetime')
        ],
        'generated_url' => $url,
        'test_links' => [
            'basic_test' => url("/test-linkedin-redirect/{$userType}/{$userId}"),
            'controller_test' => url("/test-linkedin-controller/{$userType}/{$userId}"),
            'main_oauth' => url("/api/verification/{$userType}/{$userId}/linkedin"),
            'callback' => url("/linkedin/callback")
        ],
        'user_type' => $userType,
        'user_id' => $userId
    ]);
});

// Test LinkedIn OAuth directly
Route::get('/test-direct-linkedin/{userType}/{userId}', function($userType, $userId) {
    try {
        // Test LinkedIn OAuth URL generation
        $authUrl = Laravel\Socialite\Facades\Socialite::driver('linkedin')
            ->redirect()
            ->getTargetUrl();
            
        return response()->json([
            'success' => true,
            'authorization_url' => $authUrl,
            'user_type' => $userType,
            'user_id' => $userId,
            'message' => 'LinkedIn OAuth URL generated successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Registration timeline route - must come before catch-all
Route::get('/registration/timeline/{type}/{id}', function () {
    return view('application');
});

// Catch-all route for SPA (excluding API routes)
Route::get('/{any}', function () {
    return view('application');
})->where('any', '^(?!api).*$');
