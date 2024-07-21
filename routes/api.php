<?php

// routes/api.php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/password-recovery', [AuthController::class, 'passwordRecovery']);
Route::post('/reset-password-token', [AuthController::class, 'resetPasswordWithToken']);

Route::get('/health', function () {
    return response()->json(['status' => 'OK'], 200);
});

Route::middleware(['auth:api'])->group(function () {
    Route::post('/verify-jwt', [AuthController::class, 'verifyJWT']);
    Route::post('/get-user-by-id-and-verify-jwt', [AuthController::class, 'getUserByIdAndVerifyJWTRequest']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/check-email-header', [AuthController::class, 'checkEmailHeader']);
    /**
     * TODO: Implement the following routes
     * Route::post('/verify', [AuthController::class, 'verify']);
     * Route::post('/password-recovery', [AuthController::class, 'passwordRecovery']);
     * Route::post('/reset-password', [AuthController::class, 'resetPassword']);
     */
});
