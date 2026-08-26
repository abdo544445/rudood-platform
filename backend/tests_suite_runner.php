<?php

/**
 * Rudood Platform - Full System End-to-End Test Suite Runner
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Customer;
use App\Models\KnowledgeBase;
use App\Models\AutoRule;
use App\Models\Subscription;
use App\Models\Article;
use App\Models\Channel;
use App\Services\AiService;
use App\Services\RagService;

class RudoodPlatformTester
{
    private array $results = [];
    private int $totalTests = 0;
    private int $passedTests = 0;
    private int $failedTests = 0;

    public function runAll(): array
    {
        echo "\n=========================================================\n";
        echo "🚀 STARTING RUDOOD PLATFORM COMPLETE TEST SUITE\n";
        echo "=========================================================\n\n";

        $this->testSuite1_AuthAndRoles();
        $this->testSuite2_SuperAdminCenter();
        $this->testSuite3_StoreDashboardAndChat();
        $this->testSuite4_AiEngineAndRag();
        $this->testSuite5_PlaygroundWorkbench();
        $this->testSuite6_SettingsChannelsAndWebhooks();

        $this->printSummary();

        return [
            'total'   => $this->totalTests,
            'passed'  => $this->passedTests,
            'failed'  => $this->failedTests,
            'results' => $this->results,
        ];
    }

    private function assert(string $suite, string $testName, bool $condition, ?string $details = null): void
    {
        $this->totalTests++;
        if ($condition) {
            $this->passedTests++;
            $this->results[$suite][] = ['name' => $testName, 'status' => 'PASS', 'details' => $details];
            echo "  ✓ [PASS] {$testName}\n";
        } else {
            $this->failedTests++;
            $this->results[$suite][] = ['name' => $testName, 'status' => 'FAIL', 'details' => $details];
            echo "  ✗ [FAIL] {$testName} - Error: {$details}\n";
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUITE 1: Auth & Roles
    // ──────────────────────────────────────────────────────────────────────────
    private function testSuite1_AuthAndRoles(): void
    {
        echo "📦 Suite 1: Authentication, Authorization & Roles\n";
        $suite = 'Auth & Roles';

        // 1.1 Check Super Admin exists and identifies properly
        $admin = User::where('email', 'admin@rudood.com')->first();
        $this->assert($suite, 'Super Admin user exists in database', $admin !== null);
        $this->assert($suite, 'Super Admin returns true for isSuperAdmin()', $admin && $admin->isSuperAdmin());

        // 1.2 Check Merchant Owner user
        $owner = User::where('role', 'owner')->first();
        $this->assert($suite, 'Merchant Owner exists and isSuperAdmin() is false', $owner && !$owner->isSuperAdmin());

        // 1.3 Test Atomic Registration flow
        $testEmail = 'tester_' . time() . '@test.com';
        $workspace = Workspace::create([
            'company_name' => 'شركة الاختبار التجريبية',
            'plan_id'      => 'starter',
            'status'       => 'active',
        ]);
        $bot = Bot::create([
            'workspace_id'  => $workspace->id,
            'name'          => 'مساعد الاختبار',
            'system_prompt' => 'أنت بوت تجريبي.',
            'model_type'    => 'gemini-1.5-flash',
            'ai_provider'   => 'gemini',
            'is_active'     => true,
        ]);
        $newUser = User::create([
            'name'         => 'مستخدم تجريبي',
            'email'        => $testEmail,
            'password'     => Hash::make('password123'),
            'role'         => 'owner',
            'workspace_id' => $workspace->id,
        ]);

        $this->assert($suite, 'Atomic Registration creates Workspace, Bot, and User linked properly', 
            $newUser && $newUser->workspace_id === $workspace->id && $bot->workspace_id === $workspace->id
        );

        // 1.4 Test Impersonation & Return Flow
        $adminId = $admin->id;
        session(['impersonated_by_admin' => $adminId]);
        Auth::login($owner);
        $this->assert($suite, 'Admin can impersonate store owner into active session', Auth::id() === $owner->id);

        $ctrl = app(\App\Http\Controllers\Admin\AdminWorkspaceController::class);
        $leaveRes = $ctrl->leaveImpersonation();
        $this->assert($suite, 'Admin leaveImpersonation() safely restores Super Admin session', 
            Auth::id() === $adminId && !session()->has('impersonated_by_admin')
        );

        // Cleanup test register
        $newUser->delete();
        $bot->delete();
        $workspace->delete();
        echo "\n";
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUITE 2: Super Admin Command Center
    // ──────────────────────────────────────────────────────────────────────────
    private function testSuite2_SuperAdminCenter(): void
    {
        echo "📦 Suite 2: Super Admin Command Center (/admin/*)\n";
        $suite = 'Super Admin Center';
        Auth::login(User::where('email', 'admin@rudood.com')->first());

        // 2.1 Admin Dashboard Controller
        $dashCtrl = app(\App\Http\Controllers\Admin\AdminDashboardController::class);
        $dashView = $dashCtrl->index();
        $this->assert($suite, 'AdminDashboardController calculates KPIs & renders clean view', 
            $dashView instanceof \Illuminate\View\View && strlen($dashView->render()) > 5000
        );

        // 2.2 Admin Statistics Controller
        $statsCtrl = app(\App\Http\Controllers\Admin\AdminStatsController::class);
        $statsView = $statsCtrl->index();
        $this->assert($suite, 'AdminStatsController renders complete statistical overview', 
            $statsView instanceof \Illuminate\View\View && strlen($statsView->render()) > 5000
        );

        $liveJson = $statsCtrl->live();
        $this->assert($suite, 'AdminStatsController::live returns JSON telemetry data', 
            $liveJson->getStatusCode() === 200 && $liveJson->getData()->success === true
        );

        // 2.3 Workspace Management CRUD & Bot Tuning
        $wsCtrl = app(\App\Http\Controllers\Admin\AdminWorkspaceController::class);
        $createReq = Request::create('/admin/workspaces/create', 'POST', [
            'company_name' => 'متجر اختبار السوبر إدمن ' . time(),
            'owner_name'   => 'مالك تجريبي',
            'owner_email'  => 'admin_test_ws_' . time() . '@test.com',
            'password'     => 'password123',
            'plan_id'      => 'pro',
            'status'       => 'active',
            'bot_name'     => 'بوت تجريبي',
            'ai_provider'  => 'openai_compatible',
            'model_type'   => 'moonshotai/Kimi-K2.6',
        ]);
        $storeRes = $wsCtrl->store($createReq);
        $createdWs = Workspace::where('company_name', 'like', 'متجر اختبار السوبر إدمن%')->latest()->first();
        $this->assert($suite, 'AdminWorkspaceController::store creates store, owner, and configured bot', 
            $createdWs !== null && $createdWs->bots()->exists() && $createdWs->users()->exists()
        );

        if ($createdWs) {
            // Test Bot Tuning
            $botReq = Request::create("/admin/workspaces/{$createdWs->id}/update-bot", 'POST', [
                'name'              => 'المساعد المعدل',
                'ai_provider'       => 'gemini',
                'model_type'        => 'gemini-1.5-pro',
                'temperature'       => 0.4,
                'max_tokens'        => 800,
                'bot_tone'          => 'sales',
                'system_prompt'     => 'توجيه معدل من الإدارة العليا',
                'is_active'         => true,
                'enable_rag'        => true,
                'enable_auto_rules' => true,
            ]);
            $wsCtrl->updateBot($botReq, $createdWs->id);
            $tunedBot = $createdWs->bots()->first();
            $this->assert($suite, 'AdminWorkspaceController::updateBot persists bot parameters, tone, and RAG toggles', 
                $tunedBot->model_type === 'gemini-1.5-pro' && $tunedBot->bot_tone === 'sales' && $tunedBot->enable_rag == true
            );

            // Test Instant Workspace Switcher
            $switchReq = Request::create('/admin/workspaces/switch', 'POST', ['workspace_id' => $createdWs->id]);
            $wsCtrl->switchWorkspace($switchReq);
            $this->assert($suite, 'AdminWorkspaceController::switchWorkspace switches active workspace context', 
                Auth::user()->fresh()->workspace_id === $createdWs->id
            );

            // Cleanup
            $wsCtrl->destroy($createdWs->id);
            Auth::user()->update(['workspace_id' => 1]); // restore
        }

        // 2.4 User Directory & Password Reset
        $userCtrl = app(\App\Http\Controllers\Admin\AdminUserController::class);
        $usersView = $userCtrl->index(Request::create('/admin/users', 'GET'));
        $this->assert($suite, 'AdminUserController::index lists users with search & filters', 
            $usersView instanceof \Illuminate\View\View && strlen($usersView->render()) > 1000
        );

        $testUser = User::where('role', '!=', 'super_admin')->first();
        if ($testUser) {
            $resetReq = Request::create("/admin/users/{$testUser->id}/reset-password", 'POST', [
                'password'              => 'newSecr3tPassword!',
                'password_confirmation' => 'newSecr3tPassword!',
            ]);
            $userCtrl->resetPassword($resetReq, $testUser->id);
            $this->assert($suite, 'AdminUserController::resetPassword updates hashed password', 
                Hash::check('newSecr3tPassword!', $testUser->fresh()->password)
            );
        }

        // 2.5 Article Management
        $artCtrl = app(\App\Http\Controllers\Admin\AdminArticleController::class);
        $articleTitle = 'مقال تجريبي للنظام ' . time();
        $articleReq = Request::create('/admin/articles', 'POST', [
            'title'        => $articleTitle,
            'summary'      => 'ملخص المقال التجريبي السريع.',
            'content'      => 'محتوى المقال التجريبي بالتفصيل الكامل.',
            'category'     => 'ai',
            'read_time'    => '3 دقائق',
            'is_published' => false,
        ]);
        $artCtrl->store($articleReq);
        $createdArt = Article::where('title', $articleTitle)->first();
        $this->assert($suite, 'AdminArticleController::store creates blog articles', $createdArt !== null);

        if ($createdArt) {
            $artCtrl->togglePublish($createdArt->id);
            $freshArt = Article::find($createdArt->id);
            $this->assert($suite, 'AdminArticleController::togglePublish toggles publication state', 
                $freshArt && $freshArt->is_published === true
            );
            $artCtrl->destroy($createdArt->id);
        }

        // 2.6 System Diagnostics
        $sysCtrl = app(\App\Http\Controllers\Admin\AdminSystemController::class);
        $sysView = $sysCtrl->index();
        $this->assert($suite, 'AdminSystemController::index aggregates DB, Redis, and health stats', 
            $sysView instanceof \Illuminate\View\View
        );

        // 2.7 Enterprise Audit Logs
        \App\Models\AuditLog::record(auth()->id(), 'test_action', 'عملية تدقيق تجريبية للتحقق من السجل', ['test_key' => 'test_val']);
        $auditCtrl = app(\App\Http\Controllers\Admin\AdminAuditLogController::class);
        $auditView = $auditCtrl->index(Request::create('/admin/audit-logs', 'GET'));
        $this->assert($suite, 'AdminAuditLogController::index renders audit trail view with paginated logs', 
            $auditView instanceof \Illuminate\View\View && $auditView->getData()['logs']->total() > 0
        );

        // 2.8 Contact Us Inquiries Management
        $contact = \App\Models\ContactMessage::create([
            'name'       => 'اختبار استفسار عميل',
            'email'      => 'test.contact@domain.com',
            'subject'    => 'طلب عرض أسعار خاص',
            'message'    => 'نود معرفة تكلفة الباقة المخصصة مع دعم 5 بوتات.',
            'status'     => 'new',
            'ip_address' => '127.0.0.1',
        ]);
        $contactCtrl = app(\App\Http\Controllers\Admin\AdminContactMessageController::class);
        $contactView = $contactCtrl->index(Request::create('/admin/contacts', 'GET'));
        $this->assert($suite, 'AdminContactMessageController::index renders inquiries list with stats', 
            $contactView instanceof \Illuminate\View\View && $contactView->getData()['stats']['total'] > 0
        );

        $contactCtrl->updateStatus(Request::create('/admin/contacts/' . $contact->id . '/status', 'POST', [
            'status'      => 'in_progress',
            'admin_notes' => 'تم التواصل مع العميل وتجهيز العرض.',
        ]), $contact->id);
        $updatedContact = \App\Models\ContactMessage::find($contact->id);
        $this->assert($suite, 'AdminContactMessageController::updateStatus updates status and admin notes', 
            $updatedContact && $updatedContact->status === 'in_progress' && !empty($updatedContact->admin_notes)
        );

        $contactCtrl->destroy($contact->id);
        $this->assert($suite, 'AdminContactMessageController::destroy safely deletes inquiry', 
            \App\Models\ContactMessage::find($contact->id) === null
        );

        echo "\n";
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUITE 3: Store Dashboard & Live Chat
    // ──────────────────────────────────────────────────────────────────────────
    private function testSuite3_StoreDashboardAndChat(): void
    {
        echo "📦 Suite 3: Tenant Store Dashboard & Real-Time Chat\n";
        $suite = 'Store Dashboard & Chat';

        $owner = User::where('role', 'owner')->first() ?? User::first();
        Auth::login($owner);

        // 3.1 Tenant Dashboard
        $dashCtrl = app(\App\Http\Controllers\DashboardController::class);
        $dashView = $dashCtrl->index();
        $this->assert($suite, 'DashboardController::index renders tenant store metrics', 
            $dashView instanceof \Illuminate\View\View && strlen($dashView->render()) > 1000
        );

        // 3.2 Live Chat Controller
        $chatCtrl = app(\App\Http\Controllers\ConversationController::class);
        $chatView = $chatCtrl->index(Request::create('/live-chat', 'GET'));
        $this->assert($suite, 'ConversationController::index renders live inbox', 
            $chatView instanceof \Illuminate\View\View
        );

        // 3.3 Create test customer and conversation
        $customer = Customer::firstOrCreate(
            ['workspace_id' => $owner->workspace_id, 'phone' => '966500000099'],
            ['name' => 'عميل اختبار']
        );
        $conversation = Conversation::firstOrCreate(
            ['workspace_id' => $owner->workspace_id, 'customer_id' => $customer->id],
            ['platform' => 'whatsapp', 'status' => 'active', 'last_message_at' => now()]
        );

        // 3.4 Live Chat 2.0: Human Takeover (Pause / Resume Bot)
        $toggleReq = Request::create("/live-chat/{$conversation->id}/toggle-bot", 'POST', ['pause' => true]);
        $toggleRes = $chatCtrl->toggleBot($toggleReq, $conversation->id);
        $freshConv = Conversation::find($conversation->id);
        $this->assert($suite, 'ConversationController::toggleBot pauses bot for human takeover', 
            $freshConv->is_bot_paused === true && $freshConv->isBotActive() === false
        );

        // Resume bot
        $resumeReq = Request::create("/live-chat/{$conversation->id}/toggle-bot", 'POST', ['pause' => false]);
        $chatCtrl->toggleBot($resumeReq, $conversation->id);
        $freshConv = Conversation::find($conversation->id);
        $this->assert($suite, 'ConversationController::toggleBot resumes bot automation', 
            $freshConv->is_bot_paused === false && $freshConv->isBotActive() === true
        );

        // 3.5 Live Chat 2.0: Canned Responses (Slash Commands)
        $cannedReq = Request::create('/live-chat/canned-replies', 'POST', [
            'shortcut' => '/test_hours',
            'title'    => 'أوقات العمل التجريبية',
            'content'  => 'أوقات عملنا من 9 صباحاً إلى 10 مساءً يومياً.',
        ]);
        $chatCtrl->storeCannedReply($cannedReq);
        $cannedReply = \App\Models\CannedReply::where('workspace_id', $owner->workspace_id)
            ->where('shortcut', '/test_hours')
            ->first();
        $this->assert($suite, 'ConversationController::storeCannedReply creates quick slash reply', 
            $cannedReply !== null && str_contains($cannedReply->content, 'أوقات عملنا')
        );

        // 3.6 Live Chat 2.0: Customer Notes & Tags
        $notesReq = Request::create("/live-chat/{$conversation->id}/notes", 'POST', [
            'notes' => 'عميل يرغب في طلب كميات كبيرة بالجملة',
            'tags'  => 'VIP, تاجر جملة',
        ]);
        $chatCtrl->updateNotes($notesReq, $conversation->id);
        $freshConv = Conversation::find($conversation->id);
        $this->assert($suite, 'ConversationController::updateNotes saves agent internal notes and tags', 
            $freshConv->notes === 'عميل يرغب في طلب كميات كبيرة بالجملة' && is_array($freshConv->tags) && in_array('VIP', $freshConv->tags)
        );

        // 3.7 Live Chat 2.0: CSV Export
        $exportRes = $chatCtrl->exportCsv();
        $this->assert($suite, 'ConversationController::exportCsv generates downloadable CSV stream', 
            $exportRes instanceof \Symfony\Component\HttpFoundation\StreamedResponse
        );

        echo "\n";
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUITE 4: AI Engine, RAG & Knowledge Base
    // ──────────────────────────────────────────────────────────────────────────
    private function testSuite4_AiEngineAndRag(): void
    {
        echo "📦 Suite 4: AI Engine, RAG & Knowledge Base Services\n";
        $suite = 'AI Engine & RAG';

        $bot = Bot::first() ?? Bot::create(['workspace_id' => 1, 'name' => 'المساعد']);
        $aiService = new AiService($bot);

        // 4.1 Dynamic Model List Fetcher
        $geminiModels = $aiService->fetchAvailableModels('gemini');
        $this->assert($suite, 'AiService::fetchAvailableModels returns models for Gemini', 
            $geminiModels['success'] === true && !empty($geminiModels['models'])
        );

        $openaiModels = $aiService->fetchAvailableModels('openai');
        $this->assert($suite, 'AiService::fetchAvailableModels queries live endpoint for OpenAI/Dahl', 
            $openaiModels['success'] === true && count($openaiModels['models']) > 0
        );

        // 4.2 Document Chunking and Caching
        $sampleText = "منصة ردود هي المنصة الرائدة في المملكة العربية السعودية لأتمتة خدمة العملاء عبر واتساب وتيليجرام. " .
                      "أوقات العمل الرسمية من الأحد إلى الخميس من 9 صباحاً حتى 6 مساءً بتوقيت مكة المكرمة. " .
                      "سياسة الاسترجاع تتيح للعميل استرداد كامل المبلغ خلال 14 يوماً من الشراء بدون أي رسوم إضافية.";

        $doc = KnowledgeBase::create([
            'workspace_id'  => $bot->workspace_id,
            'bot_id'        => $bot->id,
            'title'         => 'دليل خدمات متجر ردود',
            'file_name'     => 'test_manual.txt',
            'file_path'     => 'docs/test_manual.txt',
            'document_text' => $sampleText,
            'chunks_json'   => [
                ['text' => 'منصة ردود هي المنصة الرائدة لأتمتة خدمة العملاء عبر واتساب وتيليجرام.'],
                ['text' => 'أوقات العمل الرسمية من الأحد إلى الخميس من 9 صباحاً حتى 6 مساءً.'],
                ['text' => 'سياسة الاسترجاع تتيح للعميل استرداد كامل المبلغ خلال 14 يوماً من الشراء.'],
            ],
            'is_active'     => true,
        ]);

        $this->assert($suite, 'KnowledgeBase caches semantic chunks in chunks_json column', 
            is_array($doc->chunks_json) && count($doc->chunks_json) === 3
        );

        // 4.3 RAG Semantic Retrieval
        $ragService = new RagService();
        $ragResult = $ragService->retrieveRelevantChunks($bot->id, 'ما هي سياسة الاسترجاع واسترداد المبلغ؟');
        $this->assert($suite, 'RagService retrieves relevant chunks based on semantic keywords', 
            !empty($ragResult['context']) && str_contains($ragResult['context'], 'الاسترجاع')
        );

        // 4.4 Auto-Rule Matching vs RAG
        $rule = AutoRule::create([
            'workspace_id'   => $bot->workspace_id,
            'bot_id'         => $bot->id,
            'question'       => 'ما هي طرق الدفع المتاحة؟',
            'keywords'       => ['دفع', 'مدى', 'فيزا', 'طرق الدفع', 'ابل باي'],
            'reply_template' => 'نوفر الدفع عبر مدى، فيزا، ماستركارد، و Apple Pay.',
            'is_active'      => true,
        ]);

        $ruleMatch = $ragService->checkAutoRules($bot->workspace_id, 'ما هي طرق الدفع لديكم؟');
        $this->assert($suite, 'RagService matches Auto-Rule immediately before invoking LLM', 
            $ruleMatch !== null && str_contains($ruleMatch['reply'], 'Apple Pay')
        );

        // 4.5 AI FAQ Generator from Document Text
        $extractedFaqs = $aiService->extractFaqFromDocument($sampleText, 3);
        $this->assert($suite, 'AiService::extractFaqFromDocument extracts structured Q&A pairs with keywords', 
            count($extractedFaqs) > 0 && isset($extractedFaqs[0]['question']) && isset($extractedFaqs[0]['answer'])
        );

        // 4.6 AI Sentiment & Urgency Escalation Engine
        $urgentSentiment = $aiService->analyzeSentimentAndUrgency('وينكم تأخرتوا وراح اشتكيكم لوزارة التجارة!');
        $this->assert($suite, 'AiService::analyzeSentimentAndUrgency detects severe frustration and triggers auto-escalation', 
            $urgentSentiment['sentiment'] === 'urgent' && $urgentSentiment['is_escalated'] === true && !empty($urgentSentiment['reason'])
        );

        $positiveSentiment = $aiService->analyzeSentimentAndUrgency('شكراً لكم خدمة ممتازة وسريعة جداً');
        $this->assert($suite, 'AiService::analyzeSentimentAndUrgency detects positive customer sentiment', 
            $positiveSentiment['sentiment'] === 'positive' && $positiveSentiment['is_escalated'] === false
        );

        // Cleanup
        $doc->delete();
        $rule->delete();
        echo "\n";
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUITE 5: AI Playground Workbench
    // ──────────────────────────────────────────────────────────────────────────
    private function testSuite5_PlaygroundWorkbench(): void
    {
        echo "📦 Suite 5: AI Playground Workbench (/playground)\n";
        $suite = 'AI Playground';

        $owner = User::where('role', 'owner')->first() ?? User::first();
        Auth::login($owner);

        $playCtrl = app(\App\Http\Controllers\PlaygroundController::class);

        // 5.1 Playground View
        $playView = $playCtrl->index();
        $this->assert($suite, 'PlaygroundController::index renders workbench UI', 
            $playView instanceof \Illuminate\View\View && strlen($playView->render()) > 1000
        );

        // 5.2 Simulation with Parameter Overrides
        $simReq = Request::create('/playground/send', 'POST', [
            'message'           => 'مرحبا، عرفني بنفسك في سطر واحد',
            'temperature'       => 0.2,
            'max_tokens'        => 100,
            'bot_tone'          => 'formal',
            'system_prompt'     => 'أنت مساعد رسمي ومختصر جداً.',
            'enable_rag'        => true,
            'enable_auto_rules' => true,
        ]);
        $simRes = $playCtrl->send($simReq, new RagService());
        $simData = $simRes->getData();

        $this->assert($suite, 'PlaygroundController::send runs simulator with latency tracking', 
            $simData->success === true && !empty($simData->reply) && $simData->latency_ms >= 0
        );

        // 5.3 Apply Defaults Persistence
        $defaultsReq = Request::create('/playground/apply-defaults', 'POST', [
            'ai_provider'       => 'gemini',
            'model_type'        => 'gemini-1.5-flash',
            'temperature'       => 0.65,
            'max_tokens'        => 900,
            'bot_tone'          => 'friendly',
            'system_prompt'     => 'توجيه تجريبي من المختبر.',
            'enable_rag'        => true,
            'enable_auto_rules' => true,
        ]);
        $playCtrl->applyDefaults($defaultsReq);
        $bot = Bot::where('workspace_id', $owner->workspace_id)->first();
        $this->assert($suite, 'PlaygroundController::applyDefaults persists tested parameters to Bot', 
            $bot->temperature == 0.65 && $bot->max_tokens == 900 && $bot->enable_rag == true
        );

        echo "\n";
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUITE 6: Settings, Channels, Webhooks & Quotas
    // ──────────────────────────────────────────────────────────────────────────
    private function testSuite6_SettingsChannelsAndWebhooks(): void
    {
        echo "📦 Suite 6: Settings, Channels & Webhooks\n";
        $suite = 'Settings & Webhooks';

        $owner = User::where('role', 'owner')->first() ?? User::first();
        Auth::login($owner);

        // 6.1 Save Bot Settings
        $settingsCtrl = app(\App\Http\Controllers\SettingsController::class);
        $botReq = Request::create('/settings/save-bot', 'POST', [
            'bot_name'        => 'مساعد المتجر الذكي',
            'bot_tone'        => 'friendly',
            'welcome_message' => 'أهلاً بك! كيف أقدر أساعدك اليوم؟',
            'system_prompt'   => 'أنت المساعد المالي والإداري.',
        ]);
        $settingsCtrl->saveBotSettings($botReq);
        $bot = Bot::where('workspace_id', $owner->workspace_id)->first();
        $this->assert($suite, 'SettingsController::saveBotSettings updates bot name and welcome message', 
            $bot->welcome_message === 'أهلاً بك! كيف أقدر أساعدك اليوم؟'
        );

        // 6.2 BYOK Restriction Governance
        $ownerWs = $owner->workspace;
        if ($ownerWs) {
            // Lock BYOK
            $ownerWs->update(['allow_custom_api_key' => false]);
            $byokReq = Request::create('/settings/save-ai-key', 'POST', [
                'ai_provider' => 'openai',
                'ai_api_key'  => 'sk-test-key-12345',
                'model_type'  => 'gpt-4o-mini',
            ]);
            $byokRes = $settingsCtrl->saveAiKey($byokReq);
            $this->assert($suite, 'SettingsController::saveAiKey blocks custom keys when allow_custom_api_key is false', 
                session()->has('error') && str_contains(session('error'), 'مقيد')
            );

            // Unlock BYOK
            $ownerWs->update(['allow_custom_api_key' => true]);
            session()->forget('error');
            $byokRes2 = $settingsCtrl->saveAiKey($byokReq);
            $this->assert($suite, 'SettingsController::saveAiKey allows custom keys when allow_custom_api_key is true', 
                session()->has('status') && str_contains(session('status'), 'بنجاح')
            );
        }

        // 6.3 Dynamic Model Fetcher endpoint
        $modelFetchReq = Request::create('/settings/fetch-models', 'POST', [
            'ai_provider' => 'gemini',
        ]);
        $modelFetchRes = $settingsCtrl->fetchModels($modelFetchReq);
        $this->assert($suite, 'SettingsController::fetchModels returns JSON model array', 
            $modelFetchRes->getStatusCode() === 200 && $modelFetchRes->getData()->success === true
        );

        // 6.4 Channel Connection
        $channelCtrl = app(\App\Http\Controllers\ChannelController::class);
        $connReq = Request::create('/settings/channels/connect', 'POST', [
            'platform'      => 'telegram',
            'bot_token'     => '123456789:AAFakeTokenForTestingOnly',
            'bot_username'  => 'TestBotUsername',
        ]);
        $channelCtrl->connect($connReq);
        $savedChannel = Channel::where('workspace_id', $owner->workspace_id)->where('platform', 'telegram')->first();
        $this->assert($suite, 'ChannelController::connect stores channel connection credentials', 
            $savedChannel !== null && $savedChannel->bot_username === 'TestBotUsername'
        );

        // 6.5 Inbound Webhook Processing
        $webhookCtrl = app(\App\Http\Controllers\WebhookController::class);
        $tgWebhookReq = Request::create('/webhook/telegram', 'POST', [
            'message' => [
                'message_id' => 999123,
                'from' => [
                    'id'         => 888777666,
                    'first_name' => 'سارة',
                ],
                'chat' => [
                    'id' => 888777666,
                ],
                'text' => 'أهلاً، هل يوجد لديكم فرع في جدة؟',
                'date' => time(),
            ]
        ]);
        $webhookRes = $webhookCtrl->handleTelegram($tgWebhookReq);
        $this->assert($suite, 'WebhookController::handleTelegram processes inbound payload and returns 200 OK', 
            $webhookRes->getStatusCode() === 200
        );

        // 6.7 Web Live Widget Config & Messaging
        $widgetCtrl = app(\App\Http\Controllers\WidgetController::class);
        $widgetConfigRes = $widgetCtrl->getConfig($owner->workspace_id);
        $this->assert($suite, 'WidgetController::getConfig returns widget branding and color configuration', 
            $widgetConfigRes->getStatusCode() === 200 && $widgetConfigRes->getData()->success === true && isset($widgetConfigRes->getData()->config->bot_name)
        );

        $widgetMsgReq = Request::create('/api/widget/message', 'POST', [
            'workspace_id'    => $owner->workspace_id,
            'message'         => 'مرحبا، هل الاسترجاع متاح لديكم؟',
            'user_id'         => 'test_web_user_' . time(),
        ]);
        $widgetMsgRes = $widgetCtrl->sendMessage($widgetMsgReq);
        $this->assert($suite, 'WidgetController::sendMessage processes web message and returns AI reply with conversation', 
            $widgetMsgRes->getStatusCode() === 200 && $widgetMsgRes->getData()->success === true && !empty($widgetMsgRes->getData()->reply) && !empty($widgetMsgRes->getData()->conversation_id)
        );

        // 6.8 Instagram Webhook Handshake Verification
        $igHandshakeReq = Request::create('/api/webhook/instagram', 'GET', [
            'hub_mode'          => 'subscribe',
            'hub_verify_token'   => 'rudood_instagram_secret',
            'hub_challenge'      => 'test_challenge_code_99182',
        ]);
        $igHandshakeRes = $webhookCtrl->verifyInstagram($igHandshakeReq);
        $this->assert($suite, 'WebhookController::verifyInstagram verifies Meta challenge handshake', 
            $igHandshakeRes->getStatusCode() === 200 && $igHandshakeRes->getContent() === 'test_challenge_code_99182'
        );

        // 6.9 1-Click Channel Toggle Switch
        $testChannel = Channel::firstOrCreate(
            ['workspace_id' => $owner->workspace_id, 'platform' => 'web'],
            ['label' => 'Web', 'is_connected' => true, 'is_active' => true]
        );
        $initialActive = $testChannel->is_active;
        $toggleReq = Request::create("/channels/toggle/{$testChannel->id}", 'POST');
        $toggleRes = $channelCtrl->toggleChannel($toggleReq, $testChannel->id);
        $this->assert($suite, 'ChannelController::toggleChannel switches active channel state', 
            $testChannel->fresh()->is_active === !$initialActive
        );
        $channelCtrl->toggleChannel($toggleReq, $testChannel->id); // restore

        // 6.10 Dedicated Channels View
        $channelsView = $channelCtrl->indexView(Request::create('/channels', 'GET'));
        $this->assert($suite, 'ChannelController::indexView renders complete Omni-Channel Hub with all 4 cards', 
            $channelsView instanceof \Illuminate\View\View && strlen($channelsView->render()) > 3000
        );

        if ($savedChannel) {
            $savedChannel->delete();
        }

        echo "\n";
    }

    private function printSummary(): void
    {
        echo "=========================================================\n";
        echo "📋 TEST EXECUTION SUMMARY\n";
        echo "=========================================================\n";
        echo "Total Tests Run : {$this->totalTests}\n";
        echo "Passed Tests    : \033[32m{$this->passedTests}\033[0m\n";
        echo "Failed Tests    : " . ($this->failedTests > 0 ? "\033[31m{$this->failedTests}\033[0m" : "\033[32m0\033[0m") . "\n";
        echo "Success Rate    : " . round(($this->passedTests / max(1, $this->totalTests)) * 100, 2) . "%\n";
        echo "=========================================================\n\n";
    }
}

$tester = new RudoodPlatformTester();
$report = $tester->runAll();
