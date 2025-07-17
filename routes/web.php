<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailVerificationController;

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

// Catch-all route for SPA (excluding API routes)
Route::get('/{any}', function () {
    return view('application');
})->where('any', '^(?!api).*$');
