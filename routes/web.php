<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailVerificationController;

Route::get('/verify/{token}', [EmailVerificationController::class, 'verifyByToken']);

Route::get('{any?}', function () {
    return view('application');
})->where('any', '.*');
