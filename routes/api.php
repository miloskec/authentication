<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware('api')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-jwt', [AuthController::class, 'verifyJWT']);
    Route::post('/get-user-by-id-and-verify-jwt', [AuthController::class, 'getUserByIdAndVerifyJWTRequest']);

    /** 
     * TODO: Implement the following routes
     * Route::post('/verify', [AuthController::class, 'verify']);
     * Route::post('/password-recovery', [AuthController::class, 'passwordRecovery']);
     * Route::post('/reset-password', [AuthController::class, 'resetPassword']);
     * Route::post('/refresh-token', [AuthController::class, 'refresh']);
     */
});
