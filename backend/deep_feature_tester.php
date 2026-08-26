<?php

/**
 * Rudood Platform - Deep New Features Testing Script
 * Tests every single newly added feature with live assertions and payload checks.
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\CannedReply;
use App\Models\AuditLog;
use App\Services\AiService;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=========================================================\n";
echo "🔬 DEEP NEW FEATURES VERIFICATION & LIVE TELEMETRY TEST\n";
echo "=========================================================\n\n";

$passCount = 0;
$failCount = 0;

function runTest(string $title, callable $test) {
    global $passCount, $failCount;
    try {
        $result = $test();
        if ($result === true || (is_array($result) && ($result['ok'] ?? false))) {
            echo "  \033[32m✓ [PASS]\033[0m {$title}\n";
            if (is_array($result) && !empty($result['detail'])) {
                echo "    ↳ \033[90m{$result['detail']}\033[0m\n";
            }
            $passCount++;
        } else {
            echo "  \033[31m✗ [FAIL]\033[0m {$title}\n";
            $failCount++;
        }
    } catch (\Throwable $e) {
        echo "  \033[31m✗ [FAIL]\033[0m {$title} - Error: {$e->getMessage()}\n";
        $failCount++;
    }
}

// ── SETUP ──────────────────────────────────────────────────────────────────
$superAdmin = User::where('role', 'super_admin')->first();
$owner = User::where('role', 'owner')->first() ?? User::first();
$workspace = $owner->workspace ?? Workspace::first();
$bot = $workspace->bots()->first() ?? Bot::first();

Auth::login($owner);

// ── 1. LIVE CHAT 2.0: HUMAN TAKEOVER (BOT PAUSE / RESUME) ──────────────────
echo "1️⃣ Testing Live Chat 2.0: Human Takeover (Bot Pause & Resume)\n";

$customer = Customer::firstOrCreate(
    ['workspace_id' => $workspace->id, 'phone' => '+966555123456'],
    ['name' => 'سلطان المحمدي', 'platform' => 'whatsapp']
);

$conversation = Conversation::firstOrCreate(
    ['workspace_id' => $workspace->id, 'customer_id' => $customer->id],
    ['status' => 'open', 'is_bot_paused' => false]
);

$chatCtrl = app(ConversationController::class);

runTest("Pause Bot for Human Takeover (is_bot_paused = true, status = human_handling)", function() use ($chatCtrl, $conversation) {
    $req = Request::create("/live-chat/{$conversation->id}/toggle-bot", 'POST', ['pause' => true, 'minutes' => 30]);
    $res = $chatCtrl->toggleBot($req, $conversation->id);
    $conv = Conversation::find($conversation->id);
    
    $ok = $conv->is_bot_paused === true 
       && $conv->isBotActive() === false 
       && $conv->status === Conversation::STATUS_HUMAN_HANDLING;
       
    return ['ok' => $ok, 'detail' => "Status: {$conv->status}, is_bot_paused: " . ($conv->is_bot_paused ? 'true' : 'false')];
});

runTest("Verify ProcessCustomerMessage skips AI reply when bot is paused", function() use ($conversation) {
    $custMsg = Message::create([
        'conversation_id' => $conversation->id,
        'sender_type'     => 'customer',
        'content'         => 'أحتاج التحدث مع موظف بشري فوراً بخصوص طلبي',
    ]);
    
    // Simulate background processing job
    $job = new \App\Jobs\ProcessCustomerMessage($conversation->id, $custMsg->id);
    $job->handle();
    
    // There should be NO new bot message generated because bot was paused
    $botReplies = Message::where('conversation_id', $conversation->id)
        ->where('sender_type', 'bot')
        ->where('id', '>', $custMsg->id)
        ->count();
        
    return ['ok' => ($botReplies === 0), 'detail' => "Zero automated bot replies dispatched (Human takeover respected)."];
});

runTest("Resume Bot Automation (is_bot_paused = false, status = open)", function() use ($chatCtrl, $conversation) {
    $req = Request::create("/live-chat/{$conversation->id}/toggle-bot", 'POST', ['pause' => false]);
    $res = $chatCtrl->toggleBot($req, $conversation->id);
    $conv = Conversation::find($conversation->id);
    
    $ok = $conv->is_bot_paused === false 
       && $conv->isBotActive() === true 
       && $conv->status === Conversation::STATUS_OPEN;
       
    return ['ok' => $ok, 'detail' => "Status: {$conv->status}, is_bot_paused: false"];
});

echo "\n";

// ── 2. LIVE CHAT 2.0: CANNED RESPONSES (SLASH COMMANDS) ───────────────────
echo "2️⃣ Testing Live Chat 2.0: Canned Responses (Slash Commands)\n";

runTest("Store and retrieve quick canned reply (/vip_discount)", function() use ($chatCtrl, $workspace) {
    $req = Request::create('/live-chat/canned-replies', 'POST', [
        'shortcut' => 'vip_discount', // without slash, should auto-prefix
        'title'    => 'خصم كبار العملاء',
        'content'  => 'يسرنا منحك خصم خاص 20% بكود (VIP20) تقديراً لولائك.',
    ]);
    $res = $chatCtrl->storeCannedReply($req);
    
    $reply = CannedReply::where('workspace_id', $workspace->id)
        ->where('shortcut', '/vip_discount')
        ->first();
        
    return ['ok' => ($reply !== null && $reply->shortcut === '/vip_discount'), 'detail' => "Shortcut: {$reply->shortcut}, Content: {$reply->content}"];
});

runTest("Default Canned Replies Auto-Seeding (/iban, /shipping, /return, /discount)", function() use ($workspace) {
    $replies = CannedReply::where('workspace_id', $workspace->id)->get();
    $shortcuts = $replies->pluck('shortcut')->toArray();
    $ok = in_array('/iban', $shortcuts) && in_array('/shipping', $shortcuts);
    return ['ok' => $ok, 'detail' => "Found " . count($shortcuts) . " shortcuts: " . implode(', ', $shortcuts)];
});

echo "\n";

// ── 3. LIVE CHAT 2.0: CUSTOMER MINI CRM (NOTES & TAGS) ────────────────────
echo "3️⃣ Testing Customer Mini CRM: Internal Notes & Tags\n";

runTest("Save Customer Tags and Internal Agent Notes", function() use ($chatCtrl, $conversation) {
    $req = Request::create("/live-chat/{$conversation->id}/notes", 'POST', [
        'notes'        => 'العميل مهتم بشراء باقة المؤسسات، يرجى تقديم عرض سعر خاص.',
        'tags'         => 'عميل_مميز, باقة_شركات, متابعة_عاجلة',
        'is_escalated' => true,
        'sentiment'    => 'positive',
    ]);
    $chatCtrl->updateNotes($req, $conversation->id);
    $conv = Conversation::find($conversation->id);
    
    $ok = $conv->notes === 'العميل مهتم بشراء باقة المؤسسات، يرجى تقديم عرض سعر خاص.'
       && is_array($conv->tags)
       && in_array('عميل_مميز', $conv->tags)
       && in_array('باقة_شركات', $conv->tags)
       && $conv->is_escalated === true;
       
    return ['ok' => $ok, 'detail' => "Tags: [" . implode(', ', $conv->tags) . "], Escalated: Yes"];
});

echo "\n";

// ── 4. AI SENTIMENT & AUTO-ESCALATION ENGINE ──────────────────────────────
echo "4️⃣ Testing AI NLP Sentiment Analysis & Auto-Escalation Engine\n";

$aiService = new AiService($bot);

runTest("Detect Legal / Ministry Threat → Urgent & Auto-Escalated", function() use ($aiService) {
    $text = "تأخرتوا أكثر من 10 أيام وما رديتوا، راح أرفع بلاغ لوزارة التجارة وأطالب باسترداد فوري لفلوسي!";
    $analysis = $aiService->analyzeSentimentAndUrgency($text);
    
    $ok = $analysis['sentiment'] === 'urgent' 
       && $analysis['is_escalated'] === true 
       && str_contains($analysis['reason'], 'وزارة التجارة');
       
    return ['ok' => $ok, 'detail' => "Detected Sentiment: {$analysis['sentiment']}, Reason: {$analysis['reason']}"];
});

runTest("Detect Dissatisfaction / Frustration → Negative Sentiment", function() use ($aiService) {
    $text = "زعلان جداً من تأخر الشحنة ليش هذا التأخير وينكم ما تردون؟";
    $analysis = $aiService->analyzeSentimentAndUrgency($text);
    
    $ok = $analysis['sentiment'] === 'negative' && $analysis['is_escalated'] === false;
    return ['ok' => $ok, 'detail' => "Detected Sentiment: {$analysis['sentiment']}"];
});

runTest("Detect Customer Praise & Gratitude → Positive Sentiment", function() use ($aiService) {
    $text = "شكراً جزيلاً لكم، خدمة ممتازة وسريعة جداً وأفضل متجر تعاملت معه!";
    $analysis = $aiService->analyzeSentimentAndUrgency($text);
    
    $ok = $analysis['sentiment'] === 'positive' && $analysis['is_escalated'] === false;
    return ['ok' => $ok, 'detail' => "Detected Sentiment: {$analysis['sentiment']}"];
});

echo "\n";

// ── 5. USAGE QUOTAS & TOKEN TRACKING ──────────────────────────────────────
echo "5️⃣ Testing Workspace Usage Quotas & Token Consumption Tracking\n";

runTest("Record usage counters and calculate percentage", function() use ($workspace) {
    $workspace->update([
        'monthly_message_limit'    => 1000,
        'messages_used_this_month' => 840,
        'tokens_used_this_month'   => 120000,
    ]);
    
    $workspace->recordUsage(10, 1500);
    $fresh = Workspace::find($workspace->id);
    
    $ok = $fresh->messages_used_this_month === 850
       && $fresh->tokens_used_this_month === 121500
       && $fresh->usage_percentage === 85
       && $fresh->hasRemainingQuota() === true;
       
    return ['ok' => $ok, 'detail' => "Used: {$fresh->messages_used_this_month}/{$fresh->monthly_message_limit} msgs (85% consumed)"];
});

runTest("Detect quota exhaustion when limit is reached", function() use ($workspace) {
    $workspace->update([
        'monthly_message_limit'    => 500,
        'messages_used_this_month' => 500,
    ]);
    
    $fresh = Workspace::find($workspace->id);
    $ok = $fresh->hasRemainingQuota() === false && $fresh->usage_percentage === 100;
    
    // Restore normal limit
    $workspace->update(['monthly_message_limit' => 2000, 'messages_used_this_month' => 50]);
    return ['ok' => $ok, 'detail' => "hasRemainingQuota() correctly returned false when limit was hit."];
});

echo "\n";

// ── 6. ENTERPRISE ACTIVITY AUDIT TRAIL ────────────────────────────────────
echo "6️⃣ Testing Enterprise Activity Audit Logs System\n";

runTest("Record and persist critical operation audit log with metadata", function() use ($workspace, $owner) {
    $log = AuditLog::record(
        'bot_prompt_updated',
        'قام مالك المتجر بتحديث التوجيه الرئيسي للبوت وتغيير النبرة إلى ودودة',
        'bot',
        ['old_tone' => 'formal', 'new_tone' => 'friendly', 'updated_by' => $owner->email],
        $workspace->id,
        $owner->id
    );
    
    $ok = $log !== null 
       && $log->action === 'bot_prompt_updated' 
       && $log->metadata['new_tone'] === 'friendly';
       
    return ['ok' => $ok, 'detail' => "Audit Log #{$log->id} created with JSON metadata."];
});

runTest("AdminAuditLogController filters and renders audit trail", function() {
    $auditCtrl = app(AdminAuditLogController::class);
    $req = Request::create('/admin/audit-logs', 'GET', ['category' => 'bot']);
    $view = $auditCtrl->index($req);
    
    $logs = $view->getData()['logs'];
    $ok = $view instanceof \Illuminate\View\View && $logs->count() > 0;
    return ['ok' => $ok, 'detail' => "Rendered view with {$logs->total()} total filtered logs."];
});

echo "\n";

// ── 7. GLOBAL COMMAND PALETTE (CMD + K) SEARCH ────────────────────────────
echo "7️⃣ Testing Global Command Palette (Cmd + K) Dynamic Search Endpoint\n";

runTest("Command palette search finds navigation pages", function() use ($superAdmin) {
    Auth::login($superAdmin);
    
    $req = Request::create('/api/command-palette/search', 'GET', ['q' => 'شات']);
    $route = app('router')->getRoutes()->getByName('command-palette.search');
    $closure = $route->getAction('uses');
    $response = $closure($req);
    $data = $response->getData(true);
    
    $ok = $response->getStatusCode() === 200 
       && !empty($data['results']) 
       && str_contains($data['results'][0]['title'], 'المحادثات المباشرة');
       
    return ['ok' => $ok, 'detail' => "Found: {$data['results'][0]['title']} ({$data['results'][0]['url']})"];
});

runTest("Command palette search finds store workspaces for Super Admin", function() use ($superAdmin, $workspace) {
    Auth::login($superAdmin);
    
    $query = mb_substr($workspace->company_name, 0, 4);
    $req = Request::create('/api/command-palette/search', 'GET', ['q' => $query]);
    $route = app('router')->getRoutes()->getByName('command-palette.search');
    $closure = $route->getAction('uses');
    $response = $closure($req);
    $data = $response->getData(true);
    
    $foundStore = false;
    foreach ($data['results'] as $res) {
        if ($res['badge'] === 'متجر') {
            $foundStore = true;
            break;
        }
    }
    
    return ['ok' => $foundStore, 'detail' => "Successfully matched store workspace ({$workspace->company_name}) in instant search palette."];
});

echo "\n";

// ── 8. LIVE CHAT CSV EXPORT ────────────────────────────────────────────────
echo "8️⃣ Testing Live Chat CSV Data Export with UTF-8 BOM\n";

runTest("Export conversations to CSV stream", function() use ($chatCtrl, $owner) {
    Auth::login($owner);
    $streamRes = $chatCtrl->exportCsv();
    
    $headers = $streamRes->headers->all();
    $isCsv = str_contains($headers['content-type'][0] ?? '', 'text/csv');
    $hasDisposition = str_contains($headers['content-disposition'][0] ?? '', 'attachment');
    
    return ['ok' => ($isCsv && $hasDisposition), 'detail' => "Content-Type: text/csv, Filename: {$headers['content-disposition'][0]}"];
});

echo "\n";

// ── 9. VIEW COMPILATION INTEGRITY ──────────────────────────────────────────
echo "9️⃣ Testing View Compilation Integrity for Upgraded Templates\n";

runTest("Compile and render Live Chat 2.0 (3-column view)", function() use ($chatCtrl) {
    $req = Request::create('/live-chat', 'GET');
    $view = $chatCtrl->index($req);
    $html = $view->render();
    
    $has3Columns = str_contains($html, 'chat-crm-sidebar') 
                && str_contains($html, 'chat-sidebar') 
                && str_contains($html, 'toggleBotBtn')
                && str_contains($html, 'canned-chip-btn');
                
    return ['ok' => $has3Columns, 'detail' => "Rendered " . strlen($html) . " bytes with Human Takeover and CRM sidebar."];
});

runTest("Compile and render Command Palette partial", function() {
    $html = view('layouts.partials.command-palette')->render();
    $ok = str_contains($html, 'commandPaletteModal') && str_contains($html, 'commandPaletteInput');
    return ['ok' => $ok, 'detail' => "Command Palette modal partial compiled cleanly."];
});

echo "\n=========================================================\n";
echo "📊 DEEP TEST SUMMARY RESULTS\n";
echo "=========================================================\n";
echo "Total Features Tested : " . ($passCount + $failCount) . "\n";
echo "Passed                : \033[32m{$passCount}\033[0m\n";
echo "Failed                : " . ($failCount > 0 ? "\033[31m{$failCount}\033[0m" : "\033[32m0\033[0m") . "\n";
echo "Success Rate          : " . round(($passCount / max(1, $passCount + $failCount)) * 100, 2) . "%\n";
echo "=========================================================\n";
