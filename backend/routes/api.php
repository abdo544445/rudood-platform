<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebhookController;

Route::middleware(['throttle:login'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
Route::get('/bots', [BotController::class, 'index']);

// Webhook / Automation endpoints with rate limiting
Route::middleware(['throttle:webhook'])->group(function () {
    Route::post('/webhook/incoming', [WebhookController::class, 'incoming']);
    Route::get('/webhook/test', [WebhookController::class, 'test']);
    Route::post('/webhook/test', [WebhookController::class, 'incoming']);

    // Meta WhatsApp Cloud API Webhook Handshake & Ingestion
    Route::get('/webhook/whatsapp', [WebhookController::class, 'verifyWhatsApp']);
    Route::post('/webhook/whatsapp', [WebhookController::class, 'handleWhatsApp']);

    // Meta Instagram Direct & Comments Webhook Handshake & Ingestion
    Route::get('/webhook/instagram', [WebhookController::class, 'verifyInstagram']);
    Route::post('/webhook/instagram', [WebhookController::class, 'handleInstagram']);

    // Telegram Bot Webhook Ingestion
    Route::match(['get', 'post'], '/webhook/telegram/{workspace_id?}', [WebhookController::class, 'handleTelegram']);

    // Web Live Chat Widget Endpoints (Embedded Script)
    Route::get('/widget/config/{workspace_id}', [\App\Http\Controllers\WidgetController::class, 'getConfig']);
    Route::post('/widget/message', [\App\Http\Controllers\WidgetController::class, 'sendMessage']);
    Route::get('/widget/history/{conversation_id}', [\App\Http\Controllers\WidgetController::class, 'getHistory']);
});

// Production Health Check Endpoint
Route::get('/health', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbStatus = 'OK';
    } catch (\Exception $e) {
        $dbStatus = 'ERROR: ' . $e->getMessage();
    }

    $statusCode = ($dbStatus === 'OK') ? 200 : 500;

    return response()->json([
        'status'    => $statusCode === 200 ? 'healthy' : 'unhealthy',
        'database'  => $dbStatus,
        'timestamp' => now()->toIso8601String(),
    ], $statusCode);
});
