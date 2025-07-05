<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Email verification routes
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware(['signed']);

Route::post('/email/resend-verification', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');

// New email verification by token
Route::get('/verify/{token}', [EmailVerificationController::class, 'verifyByToken']);

// Password reset routes
Route::post('/auth/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/auth/reset-password', [AuthController::class, 'reset'])->name('password.update');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/setup-2fa', [AuthController::class, 'setupTwoFactor']);
    Route::post('/auth/verify-2fa', [AuthController::class, 'verifyTwoFactor']);
    Route::post('/auth/disable-2fa', [AuthController::class, 'disableTwoFactor']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});