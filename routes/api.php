<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;
// use App\Http\Controllers\OtpController;

// Ensure routes are protected using authentication middleware if needed
Route::middleware('auth:sanctum')->group(function () {
    // Route::middleware('auth:sanctum')->put('/movies/{movie}', [MovieController::class, 'update']);
    // Route::middleware('auth:sanctum')->get('/movies/{movie}', [MovieController::class, 'show']);
    // Route::middleware('auth:sanctum')->get('/movies', [MovieController::class, 'index']);
    // Route::middleware('auth:sanctum')->post('/movies', [MovieController::class, 'store']);
    // Route::middleware('auth:sanctum')->delete('/movies/{movie}', [MovieController::class, 'destroy']);

    // Route::resource('movies', MovieController::class);
    Route::apiResource('movies', MovieController::class);

    // This route is protected with auth middleware
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/update-password', [ProfileController::class, 'updatePassword']);
    Route::delete('/delete-account', [ProfileController::class, 'deleteAccount']);

    // OTP
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::post('verify-token', [AuthController::class, 'verifyToken']);
// Route::post('register', [AuthController::class, 'register']);
Route::post('register', [AuthController::class, 'register']);
Route::get('verify-email/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('resend-email', [AuthController::class, 'resendVerificationEmail'])->name('verification.resend');
Route::post('login', [AuthController::class, 'login']);
// Add a route to check if the email is verified
// Route::get('check-verification', [AuthController::class, 'checkVerification']);

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'getProfile']);
// Route::middleware('auth:sanctum')->put('/profile', [ProfileController::class, 'updateProfile']);
// Route::middleware('auth:sanctum')->put('/update-password', [ProfileController::class, 'updatePassword']);
// Route::middleware('auth:sanctum')->delete('/delete-account', [ProfileController::class, 'deleteAccount']);

// Route::middleware('auth:sanctum')->post('/send-otp', [AuthController::class, 'sendOtp']);
// Route::middleware('auth:sanctum')->post('/verify-otp', [AuthController::class, 'verifyOtp']);
