<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ParticipantController;

Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',    [AuthController::class, 'me']);

    Route::get('/participants',                          [ParticipantController::class, 'index']);
    Route::post('/participants',                         [ParticipantController::class, 'store']);
    Route::get('/participants/{participant}',             [ParticipantController::class, 'show']);
    Route::put('/participants/{participant}',             [ParticipantController::class, 'update']);
    Route::delete('/participants/{participant}',          [ParticipantController::class, 'destroy']);
});
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'HackAccess API',
    ]);
});