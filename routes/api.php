<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function () {
    Route::get('/auth/{provider}/redirect', [AuthController::class, 'socialRedirect']);
    Route::get('/auth/{provider}/callback', [AuthController::class, 'socialCallback']);

    Route::post('/sign-up', [AuthController::class, 'signUp']);
    Route::post('/sign-in', [AuthController::class, 'signIn']);

    Route::middleware(['auth:api'])->group(function () {
        Route::get('/verify-email', [AuthController::class, 'requestVerifyEmail']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

        Route::get('/sign-out', [AuthController::class, 'signOut']);
    });
});
