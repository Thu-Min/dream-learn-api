<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/auth/{provider}/redirect', 'socialRedirect');
        Route::get('/auth/{prodiver}/callback', 'socialCallback');

        Route::post('/sign-up', 'signUp');
        Route::post('/sign-in', 'signIn');
    });

    Route::middleware(['auth:api'])->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/verify-email', 'requestVerifyEmail');
            Route::post('/verify-email', 'verifyEmail');

            Route::get('/sign-out', 'signOut');
        });
    });
});
