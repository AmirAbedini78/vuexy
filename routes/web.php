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

// LinkedIn OAuth callback route (this should remain in web routes for OAuth redirects)
Route::get('/linkedin/callback', [UserVerificationController::class, 'linkedinCallback']);

// Catch-all route for SPA (excluding API routes)
Route::get('/{any}', function () {
    return view('application');
})->where('any', '^(?!api).*$');
