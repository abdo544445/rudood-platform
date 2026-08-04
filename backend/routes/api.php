<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebhookController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/bots', [BotController::class, 'index']);

// Webhook / Automation endpoints
Route::post('/webhook/incoming', [WebhookController::class, 'incoming']);
Route::get('/webhook/test', [WebhookController::class, 'test']);
Route::post('/webhook/test', [WebhookController::class, 'incoming']);
