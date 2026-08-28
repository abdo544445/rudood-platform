<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ChannelController;
use App\Models\Message;
use App\Models\Conversation;

use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminStatsController;
use App\Http\Controllers\Admin\AdminWorkspaceController;
use App\Http\Controllers\Admin\AdminSystemController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\PlaygroundController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminContactMessageController;
use App\Http\Controllers\Admin\AdminSubscriberController;
use App\Models\ContactMessage;
use App\Models\AuditLog;

// ─── Public Routes (No Auth Required) ────────────────────────────────────────
Route::get('/', fn() => view('index'))->name('home');
Route::get('/index', fn() => view('index'));
Route::get('/how-it-works', fn() => view('how-it-works'))->name('how-it-works');
Route::get('/subscription-pending', [AuthController::class, 'showSubscriptionPending'])->name('subscription.pending');
Route::post('/subscribe-request', [AuthController::class, 'submitSubscriptionRequest'])->name('subscription.request');
Route::get('/maintenance', [AdminSystemController::class, 'showMaintenancePage'])->name('maintenance');
Route::get('/demo', fn() => view('demo'))->name('demo');
Route::get('/features', fn() => view('features'));
Route::get('/pricing', fn() => view('pricing'));
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/contact', fn() => view('try'))->name('contact');
Route::get('/try', fn() => view('try'));
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'sender_name'    => 'required|string|max:255',
        'sender_email'   => 'required|email|max:255',
        'sender_subject' => 'nullable|string|max:255',
        'sender_message' => 'required|string|max:2000',
    ]);

    $contact = ContactMessage::create([
        'name'       => $validated['sender_name'],
        'email'      => $validated['sender_email'],
        'subject'    => $validated['sender_subject'] ?? 'استفسار عام',
        'message'    => $validated['sender_message'],
        'status'     => 'new',
        'ip_address' => $request->ip(),
    ]);

    AuditLog::record(
        null,
        'contact.received',
        "تم استلام رسالة تواصل جديدة من {$contact->name} ({$contact->email})",
        [
            'contact_id' => $contact->id,
            'subject'    => $contact->subject,
        ]
    );

    return back()->with('success', 'شكراً لتواصلك معنا! تم استلام رسالتك وسيقوم فريق الدعم بالرد عليك قريباً.');
})->name('contact.submit');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

Route::middleware(['throttle:login'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// ─── Super Admin Routes (Admin Auth Required) ─────────────────────────────────
Route::middleware(['auth', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [AdminStatsController::class, 'index'])->name('statistics');
    Route::get('/statistics/live', [AdminStatsController::class, 'live'])->name('statistics.live');
    Route::post('/statistics/prune-failed', [AdminStatsController::class, 'pruneFailed'])->name('statistics.prune-failed');
    
    // Workspace Management
    Route::get('/workspaces', [AdminWorkspaceController::class, 'index'])->name('workspaces.index');
    Route::post('/workspaces/create', [AdminWorkspaceController::class, 'store'])->name('workspaces.store');
    Route::post('/workspaces/fetch-models', [AdminWorkspaceController::class, 'fetchModels'])->name('workspaces.fetch-models');
    Route::post('/workspaces/switch', [AdminWorkspaceController::class, 'switchWorkspace'])->name('workspaces.switch');
    Route::get('/workspaces/{id}', [AdminWorkspaceController::class, 'show'])->name('workspaces.show');
    Route::post('/workspaces/{id}/update', [AdminWorkspaceController::class, 'update'])->name('workspaces.update');
    Route::post('/workspaces/{id}/update-bot', [AdminWorkspaceController::class, 'updateBot'])->name('workspaces.update-bot');
    Route::post('/workspaces/{id}/status', [AdminWorkspaceController::class, 'updateStatus'])->name('workspaces.update-status');
    Route::post('/workspaces/{id}/plan', [AdminWorkspaceController::class, 'updatePlan'])->name('workspaces.update-plan');
    Route::post('/workspaces/{id}/impersonate', [AdminWorkspaceController::class, 'impersonate'])->name('workspaces.impersonate');
    Route::delete('/workspaces/{id}', [AdminWorkspaceController::class, 'destroy'])->name('workspaces.destroy');

    // Users & Store Owners Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/update', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Article & Blog Management
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{id}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{id}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::post('/articles/{id}/toggle-publish', [AdminArticleController::class, 'togglePublish'])->name('articles.toggle-publish');
    Route::delete('/articles/{id}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');

    // Contact Us Inquiries Management
    Route::get('/contacts', [AdminContactMessageController::class, 'index'])->name('contacts.index');
    Route::post('/contacts/{id}/status', [AdminContactMessageController::class, 'updateStatus'])->name('contacts.update-status');
    Route::delete('/contacts/{id}', [AdminContactMessageController::class, 'destroy'])->name('contacts.destroy');

    // Enterprise Audit Logs
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');

    // Subscriber Requests & Onboarding Management (Client Requirements #2 & #5)
    Route::get('/subscribers', [AdminSubscriberController::class, 'index'])->name('subscribers.index');
    Route::get('/subscribers/create', [AdminSubscriberController::class, 'create'])->name('subscribers.create');
    Route::post('/subscribers', [AdminSubscriberController::class, 'store'])->name('subscribers.store');
    Route::post('/subscribers/{id}/approve', [AdminSubscriberController::class, 'approve'])->name('subscribers.approve');
    Route::post('/subscribers/{id}/reject', [AdminSubscriberController::class, 'reject'])->name('subscribers.reject');
    Route::delete('/subscribers/{id}', [AdminSubscriberController::class, 'destroy'])->name('subscribers.destroy');

    // Infrastructure & System Health
    Route::get('/system', [AdminSystemController::class, 'index'])->name('system.index');
    Route::post('/system/maintenance', [AdminSystemController::class, 'toggleMaintenance'])->name('system.maintenance');
});

// Leave Impersonation (Available to authenticated users who were impersonated by an admin)
Route::middleware('auth')->group(function () {
    Route::get('/admin/impersonate/leave', [AdminWorkspaceController::class, 'leaveImpersonation'])->name('impersonate.leave');
    Route::post('/admin/impersonate/leave', [AdminWorkspaceController::class, 'leaveImpersonation'])->name('impersonate.leave.post');
});

// ─── Authenticated Dashboard Routes ──────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/roi-analytics', [DashboardController::class, 'getRoiAnalytics'])->name('dashboard.roi-analytics');

    // Live Chat & Chat 2.0 Actions
    Route::get('/live-chat', [ConversationController::class, 'index'])->name('live-chat.index');
    Route::get('/live-chat/export', [ConversationController::class, 'exportCsv'])->name('live-chat.export');
    Route::post('/live-chat/canned-replies', [ConversationController::class, 'storeCannedReply'])->name('live-chat.canned-replies.store');
    Route::get('/live-chat/{id}', [ConversationController::class, 'show'])->name('live-chat.show');
    Route::post('/live-chat/{id}/send', [ConversationController::class, 'sendMessage']);
    Route::post('/live-chat/{id}/send-interactive', [ConversationController::class, 'sendInteractive'])->name('live-chat.send-interactive');
    Route::post('/live-chat/{id}/attachment', [ConversationController::class, 'uploadAttachment'])->name('live-chat.attachment');
    Route::post('/live-chat/{id}/resolve', [ConversationController::class, 'resolveConversation'])->name('live-chat.resolve');
    Route::post('/live-chat/{id}/csat', [ConversationController::class, 'submitCsat'])->name('live-chat.csat');
    Route::post('/live-chat/{id}/toggle-bot', [ConversationController::class, 'toggleBot'])->name('live-chat.toggle-bot');
    Route::post('/live-chat/{id}/notes', [ConversationController::class, 'updateNotes'])->name('live-chat.update-notes');

    // AI Management
    Route::get('/ai-manage', [BotController::class, 'manageView']);
    Route::post('/ai-manage/save-bot', [BotController::class, 'saveBot']);
    Route::post('/ai-manage/save-rule', [BotController::class, 'saveRule']);
    Route::delete('/ai-manage/rule/{id}', [BotController::class, 'deleteRule']);
    Route::post('/ai-manage/upload-doc', [BotController::class, 'uploadDocument']);
    Route::post('/ai-manage/doc/{id}/generate-qa', [BotController::class, 'generateFaqFromDoc'])->name('ai.generate-faq');
    Route::delete('/ai-manage/doc/{id}', [BotController::class, 'deleteDocument']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings/save-bot', [SettingsController::class, 'saveBotSettings']);
    Route::post('/settings/save-ai-key', [SettingsController::class, 'saveAiKey']);
    Route::post('/settings/fetch-models', [SettingsController::class, 'fetchModels'])->name('settings.fetch-models');

    // Dedicated Channels & Integrations Hub
    Route::get('/channels', [ChannelController::class, 'indexView'])->name('channels.index.view');
    Route::post('/channels/toggle/{id}', [ChannelController::class, 'toggleChannel'])->name('channels.toggle');
    Route::post('/channels/widget', [ChannelController::class, 'saveWidgetSettings'])->name('channels.widget.save');
    Route::match(['get', 'post'], '/channels/verify/{id}', [ChannelController::class, 'verify'])->name('channels.verify.direct');
    Route::match(['get', 'post'], '/channels/disconnect/{id}', [ChannelController::class, 'disconnect'])->name('channels.disconnect.direct');

    // Channels (messaging integrations api/actions)
    Route::prefix('settings/channels')->name('channels.')->group(function () {
        Route::get('/', [ChannelController::class, 'index'])->name('index');
        Route::post('/connect', [ChannelController::class, 'connect'])->name('connect');
        Route::match(['get', 'post'], '/verify/{id}', [ChannelController::class, 'verify'])->name('verify');
        Route::match(['get', 'post'], '/disconnect/{id}', [ChannelController::class, 'disconnect'])->name('disconnect');
        Route::delete('/{id}', [ChannelController::class, 'destroy'])->name('destroy');
    });

    // Dedicated AI Playground (Workbench)
    Route::get('/playground', [PlaygroundController::class, 'index'])->name('playground.index');
    Route::post('/playground/send', [PlaygroundController::class, 'send'])->name('playground.send');
    Route::post('/playground/apply-defaults', [PlaygroundController::class, 'applyDefaults'])->name('playground.apply-defaults');

    // Global Command Palette Search Endpoint (AJAX)
    Route::get('/api/command-palette/search', function (Illuminate\Http\Request $request) {
        $q = mb_strtolower(trim($request->get('q', '')));
        $user = auth()->user();
        $results = [];

        // 1. Navigation Pages
        $pages = [
            ['title' => 'لوحة التحكم الرئيسية', 'subtitle' => 'الإحصائيات ونشاط المتجر', 'url' => url('/dashboard'), 'icon' => 'bi-speedometer2', 'badge' => 'صفحة'],
            ['title' => 'المحادثات المباشرة', 'subtitle' => 'صندوق الوارد والدردشة الحية', 'url' => url('/live-chat'), 'icon' => 'bi-chat-dots-fill', 'badge' => 'شات'],
            ['title' => 'مختبر الذكاء الاصطناعي', 'subtitle' => 'تجربة النماذج ومحاكاة البوت', 'url' => url('/playground'), 'icon' => 'bi-cpu-fill', 'badge' => 'مختبر'],
            ['title' => 'قواعد المعرفة والمستندات', 'subtitle' => 'إدارة المستندات والأسئلة الشائعة', 'url' => url('/ai-manage'), 'icon' => 'bi-journal-code', 'badge' => 'معرفة'],
            ['title' => 'إعدادات البوت والـ API', 'subtitle' => 'تخصيص المزود والمفاتيح', 'url' => url('/settings'), 'icon' => 'bi-gear-fill', 'badge' => 'إعدادات'],
            ['title' => 'ربط وتكامل القنوات (Omni-Channel)', 'subtitle' => 'واتساب، تيليجرام، ودجت الموقع، إنستغرام', 'url' => url('/channels'), 'icon' => 'bi-diagram-3-fill', 'badge' => 'قنوات'],
        ];

        if ($user?->isSuperAdmin()) {
            $adminPages = [
                ['title' => 'مركز قيادة الإدارة العليا', 'subtitle' => 'لوحة الـ Super Admin و MRR', 'url' => url('/admin/dashboard'), 'icon' => 'bi-shield-shaded', 'badge' => 'إدارة'],
                ['title' => 'إدارة الشركات والمتاجر', 'subtitle' => 'عرض وتعديل والتحكم بجميع المتاجر', 'url' => url('/admin/workspaces'), 'icon' => 'bi-buildings-fill', 'badge' => 'إدارة'],
                ['title' => 'دليل المستخدمين وملاك المتاجر', 'subtitle' => 'إدارة الحسابات وتعيين كلمات المرور', 'url' => url('/admin/users'), 'icon' => 'bi-people-fill', 'badge' => 'إدارة'],
                ['title' => 'التقرير الإحصائي العالمي', 'subtitle' => 'تحليلات المنصة والمحادثات', 'url' => url('/admin/statistics'), 'icon' => 'bi-bar-chart-line-fill', 'badge' => 'إحصائيات'],
                ['title' => 'سجل تدقيق الأنشطة', 'subtitle' => 'مراقبة العمليات والتغييرات', 'url' => url('/admin/audit-logs'), 'icon' => 'bi-shield-check', 'badge' => 'أمان'],
                ['title' => 'تشخيص صحة النظام والـ Redis', 'subtitle' => 'حالة الخوادم والاتصالات', 'url' => url('/admin/system'), 'icon' => 'bi-hdd-network-fill', 'badge' => 'نظام'],
            ];
            $pages = array_merge($adminPages, $pages);
        }

        foreach ($pages as $p) {
            if (empty($q) 
                || str_contains(mb_strtolower($p['title']), $q) 
                || str_contains(mb_strtolower($p['subtitle']), $q) 
                || str_contains(mb_strtolower($p['badge']), $q)
                || ($q === 'شات' && str_contains(mb_strtolower($p['url']), 'chat'))
            ) {
                $results[] = $p;
            }
        }

        // 2. Stores / Workspaces (if Super Admin)
        if ($user?->isSuperAdmin() && !empty($q)) {
            $stores = \App\Models\Workspace::where('company_name', 'like', "%{$q}%")->limit(4)->get();
            foreach ($stores as $s) {
                $results[] = [
                    'title'    => 'متجر: ' . $s->company_name,
                    'subtitle' => 'الانتقال لمعاينة وتعديل بيانات المتجر',
                    'url'      => url('/admin/workspaces/' . $s->id),
                    'icon'     => 'bi-shop',
                    'badge'    => 'متجر',
                ];
            }
        }

        return response()->json(['results' => array_slice($results, 0, 8)]);
    })->name('command-palette.search');

    // Legacy AI playground (RAG testing fallback)
    Route::post('/ai-manage/test', [BotController::class, 'testAi']);

    // Legacy Route Redirects (Ensures all URLs load the active dynamic modules)
    Route::get('/chat', fn() => redirect('/live-chat'));
    Route::get('/auto', fn() => redirect('/ai-manage'));
    Route::get('/ai', fn() => redirect('/playground'));
    Route::get('/article', fn() => redirect('/blog'));
});
