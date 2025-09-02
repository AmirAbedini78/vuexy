<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\IndividualUserController;
use App\Http\Controllers\Api\CompanyUserController;
use App\Http\Middleware\ApiMiddleware;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\ItineraryAccommodationController;
use App\Http\Controllers\Api\SpecialAddonController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;

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

// Apply API middleware to all routes
Route::middleware([ApiMiddleware::class])->group(function () {
    
    // Test route to verify API is working
    Route::get('/test', function () {
        return response()->json(['message' => 'API is working!']);
    });

    // Public routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Email verification routes
    Route::post('/email/resend-verification', [EmailVerificationController::class, 'resendWithoutAuth']);

    Route::post('/email/resend-verification', [EmailVerificationController::class, 'resend'])
        ->middleware(['auth:sanctum', 'throttle:6,1'])
        ->name('verification.send');

    // Password reset routes
    Route::post('/auth/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/auth/reset-password', [AuthController::class, 'reset'])->name('password.update');

    // Individual User routes
    Route::post('/individual-users', [IndividualUserController::class, 'store']);
    Route::get('/individual-users/{id}', [IndividualUserController::class, 'show']);

    // Company User routes
    Route::post('/company-users', [CompanyUserController::class, 'store']);
    Route::get('/company-users/{id}', [CompanyUserController::class, 'show']);

    // Listing CRUD
    Route::apiResource('listings', ListingController::class);

    // ItineraryAccommodation CRUD (nested under listing)
    Route::apiResource('listings.itineraries', ItineraryAccommodationController::class);

    // SpecialAddon CRUD (nested under listing)
    Route::apiResource('listings.special-addons', SpecialAddonController::class);

    // User Verification API
    Route::prefix('verification')->group(function () {
        Route::get('/{userType}/{userId}', [\App\Http\Controllers\Api\UserVerificationController::class, 'show']);
        Route::post('/{userType}/{userId}/email', [\App\Http\Controllers\Api\UserVerificationController::class, 'sendEmailVerification']);
        Route::post('/email/verify/{token}', [\App\Http\Controllers\Api\UserVerificationController::class, 'verifyEmail']);
        Route::post('/{userType}/{userId}/whatsapp', [\App\Http\Controllers\Api\UserVerificationController::class, 'sendWhatsappCode']);
        Route::post('/{userType}/{userId}/whatsapp/verify', [\App\Http\Controllers\Api\UserVerificationController::class, 'verifyWhatsappCode']);
        Route::get('/{userType}/{userId}/linkedin', [\App\Http\Controllers\Api\UserVerificationController::class, 'startLinkedinOAuth']);
        Route::post('/{userType}/{userId}/linkedin/verify', [\App\Http\Controllers\Api\UserVerificationController::class, 'verifyLinkedinCode']);
        Route::post('/{userType}/{userId}/profile', [\App\Http\Controllers\Api\UserVerificationController::class, 'completeProfile']);
        
        // New routes for current user (without individual/company dependency)
        Route::get('/user/{userId}', [\App\Http\Controllers\Api\UserVerificationController::class, 'showForCurrentUser']);
        Route::post('/user/{userId}/email', [\App\Http\Controllers\Api\UserVerificationController::class, 'sendEmailVerificationForCurrentUser']);
        Route::post('/user/{userId}/whatsapp', [\App\Http\Controllers\Api\UserVerificationController::class, 'sendWhatsappCodeForCurrentUser']);
        Route::post('/user/{userId}/whatsapp/verify', [\App\Http\Controllers\Api\UserVerificationController::class, 'verifyWhatsappCodeForCurrentUser']);
        Route::match(['GET', 'POST'], '/user/{userId}/linkedin', [\App\Http\Controllers\Api\UserVerificationController::class, 'startLinkedinOAuthForCurrentUser']);
        Route::post('/user/{userId}/profile', [\App\Http\Controllers\Api\UserVerificationController::class, 'completeProfileForCurrentUser']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);
        Route::post('/auth/setup-2fa', [AuthController::class, 'setupTwoFactor']);
        Route::post('/auth/verify-2fa', [AuthController::class, 'verifyTwoFactor']);
        Route::post('/auth/disable-2fa', [AuthController::class, 'disableTwoFactor']);

        // Notifications
        Route::get('/notifications', function (Request $request) {
            return $request->user()->notifications()->orderBy('created_at', 'desc')->limit(20)->get();
        });
        Route::post('/notifications/mark-read', function (Request $request) {
            $ids = $request->input('ids', []);
            foreach ($ids as $id) {
                $notification = $request->user()->notifications()->where('id', $id)->first();
                if ($notification) $notification->markAsRead();
            }
            return response()->json(['success' => true]);
        });
    });

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    // Admin routes - protected by AdminMiddleware
    Route::middleware(['auth:sanctum', AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/providers', [AdminController::class, 'providers']);
        Route::get('/providers/{id}/{type}', [AdminController::class, 'provider']);
        Route::put('/providers/{id}/{type}', [AdminController::class, 'updateProvider']);
        Route::delete('/providers/{id}/{type}', [AdminController::class, 'deleteProvider']);
        Route::put('/providers/{id}/{type}/status', [AdminController::class, 'updateProviderStatus']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/users/{id}', [AdminController::class, 'user']);
        Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
        Route::get('/listings', [AdminController::class, 'listings']);
        Route::get('/statistics', [AdminController::class, 'statistics']);
    });

    // Temporary test route for providers (without authentication)
    Route::get('/test/providers', [AdminController::class, 'providers']);
});
