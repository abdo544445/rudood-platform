<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\SettingsController;
use App\Models\Message;
use App\Models\Conversation;

// ─── Public Routes (No Auth Required) ────────────────────────────────────────
Route::get('/', fn() => redirect('/login'));
Route::get('/index', fn() => view('index'));
Route::get('/features', fn() => view('features'));
Route::get('/pricing', fn() => view('pricing'));
Route::get('/blog', fn() => view('blog'));
Route::get('/try', fn() => view('try'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ─── Authenticated Dashboard Routes ──────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        $workspace_id = auth()->user()->workspace_id;
        $total_conversations = Conversation::where('workspace_id', $workspace_id)->count();
        $total_messages = Message::whereHas('conversation', fn($q) => $q->where('workspace_id', $workspace_id))->count();
        $bot_messages = Message::where('sender_type', 'bot')
            ->whereHas('conversation', fn($q) => $q->where('workspace_id', $workspace_id))->count();
        $resolution_rate = $total_messages > 0 ? round(($bot_messages / $total_messages) * 100) : 0;
        $new_inquiries = Conversation::where('workspace_id', $workspace_id)->where('status', 'open')->count();

        $stats = [
            'total_conversations' => $total_conversations,
            'resolution_rate'     => $resolution_rate . '%',
            'new_inquiries'       => $new_inquiries,
            'avg_response_time'   => '1.5 ثانية',
        ];

        $recent_conversations = Conversation::with('customer')
            ->where('workspace_id', $workspace_id)
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recent_conversations'));
    });

    // Live Chat
    Route::get('/live-chat', [ConversationController::class, 'index'])->name('live-chat.index');
    Route::get('/live-chat/{id}', [ConversationController::class, 'show'])->name('live-chat.show');
    Route::post('/live-chat/{id}/send', [ConversationController::class, 'sendMessage']);

    // AI Management
    Route::get('/ai-manage', [BotController::class, 'manageView']);
    Route::post('/ai-manage/save-bot', [BotController::class, 'saveBot']);
    Route::post('/ai-manage/save-rule', [BotController::class, 'saveRule']);
    Route::delete('/ai-manage/rule/{id}', [BotController::class, 'deleteRule']);
    Route::post('/ai-manage/upload-doc', [BotController::class, 'uploadDocument']);
    Route::delete('/ai-manage/doc/{id}', [BotController::class, 'deleteDocument']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings/save-bot', [SettingsController::class, 'saveBotSettings']);
    Route::post('/settings/save-ai-key', [SettingsController::class, 'saveAiKey']);

    // Other pages
    Route::get('/chat', fn() => view('chat'));
    Route::get('/auto', fn() => view('auto'));
    Route::get('/ai', fn() => view('ai'));
    Route::get('/article', fn() => view('articlel'));
    Route::get('/background', fn() => view('background'));
    Route::get('/form-student', fn() => view('form_student'));
});
