<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use Illuminate\Support\Facades\Route;

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
});

Route::post('verify-token', [AuthController::class, 'verifyToken']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);