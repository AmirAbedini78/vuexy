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

// Timeline email verification route
Route::get('/verification/email/verify/{token}', [UserVerificationController::class, 'verifyEmail']);

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

// Mock LinkedIn OAuth for testing (temporary)
Route::get('/mock-linkedin/{userType}/{userId}', function($userType, $userId) {
    // Simulate successful LinkedIn verification
    $verification = \App\Models\UserVerification::firstOrCreate([
        'user_type' => $userType,
        'user_id' => $userId,
    ]);
    
    $verification->linkedin_verified = true;
    $verification->linkedin_id = 'mock_linkedin_id_' . time();
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

// Catch-all route for SPA (excluding API routes)
Route::get('/{any}', function () {
    return view('application');
})->where('any', '^(?!api).*$');
