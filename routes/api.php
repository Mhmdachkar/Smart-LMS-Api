<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication routes
    require __DIR__ . '/api/v1/auth.php';
    
    // Protected routes  
    Route::middleware('jwt.auth')->group(function () {
        require __DIR__ . '/api/v1/users.php';
        require __DIR__ . '/api/v1/courses.php';
    });
});
