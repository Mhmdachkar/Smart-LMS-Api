<?php
// File: routes/api/v1/auth.php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| All authentication-related routes for the Smart LMS API
*/

// Public authentication routes (no middleware required)
Route::prefix('auth')->group(function () {
    // User registration
    Route::post('register', [AuthController::class, 'register'])
        ->name('api.v1.auth.register');

    // User login
    Route::post('login', [AuthController::class, 'login'])
        ->name('api.v1.auth.login');

    // Protected authentication routes (require JWT token)
    Route::middleware('jwt.auth')->group(function () {
        // Get current user data
        Route::get('me', [AuthController::class, 'me'])
            ->name('api.v1.auth.me');

        // User logout
        Route::post('logout', [AuthController::class, 'logout'])
            ->name('api.v1.auth.logout');

        // Refresh JWT token
        Route::post('refresh', [AuthController::class, 'refresh'])
            ->name('api.v1.auth.refresh');
    });
});

// Available endpoints:
// POST /api/v1/auth/register  - Register new user
// POST /api/v1/auth/login     - User login
// GET  /api/v1/auth/me        - Get current user (requires token)
// POST /api/v1/auth/logout    - User logout (requires token)
// POST /api/v1/auth/refresh   - Refresh token (requires token)