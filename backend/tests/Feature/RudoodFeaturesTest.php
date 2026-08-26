<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\AutoRule;
use App\Models\Conversation;
use App\Models\Customer;
use App\Models\KnowledgeBase;
use App\Models\Message;
use App\Models\AiDecisionLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class RudoodFeaturesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test P0-3: Registration creates Workspace, Bot, and User atomically.
     */
    public function test_user_registration_creates_workspace_and_bot_atomically(): void
    {
        $response = $this->post('/register', [
            'full_name'             => 'أحمد التجريبي',
            'email'                 => 'ahmed@test.com',
            'phone'                 => '+966500000001',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'ahmed@test.com', 'role' => 'owner']);
        $this->assertDatabaseHas('workspaces', ['company_name' => "أحمد التجريبي's Workspace"]);

        $user = User::where('email', 'ahmed@test.com')->first();
        $this->assertNotNull($user->workspace_id);
        $this->assertDatabaseHas('bots', ['workspace_id' => $user->workspace_id, 'is_active' => true]);
    }

    /**
     * Test P0-1: Saving FAQ rule parses comma-separated keywords and falls back gracefully.
     */
    public function test_save_faq_rule_stores_custom_keywords_array(): void
    {
        $workspace = Workspace::create(['company_name' => 'Test Co', 'status' => 'active']);
        $bot = Bot::create(['workspace_id' => $workspace->id, 'name' => 'Bot', 'is_active' => true]);
        $user = User::create([
            'name'         => 'Owner',
            'email'        => 'owner@test.com',
            'password'     => Hash::make('password123'),
            'workspace_id' => $workspace->id,
            'role'         => 'owner',
        ]);

        $this->actingAs($user)->post('/ai-manage/save-rule', [
            'question' => 'ما هي أوقات التوصيل لديكم؟',
            'keywords' => 'توصيل, مدة, شحن, اوقات',
            'answer'   => 'التوصيل خلال 24 ساعة داخل الرياض.',
        ])->assertSessionHasNoErrors();

        $rule = AutoRule::where('workspace_id', $workspace->id)->first();
        $this->assertNotNull($rule);
        $this->assertContains('توصيل', $rule->keywords);
        $this->assertContains('مدة', $rule->keywords);
        $this->assertNotContains('ما', $rule->keywords);
    }

    /**
     * Test P0-2: Save bot method updates bot personality and prompt.
     */
    public function test_save_bot_updates_personality_and_prompt(): void
    {
        $workspace = Workspace::create(['company_name' => 'Test Co', 'status' => 'active']);
        $bot = Bot::create(['workspace_id' => $workspace->id, 'name' => 'Old Bot', 'is_active' => true]);
        $user = User::create([
            'name'         => 'Owner',
            'email'        => 'owner2@test.com',
            'password'     => Hash::make('password123'),
            'workspace_id' => $workspace->id,
            'role'         => 'owner',
        ]);

        $this->actingAs($user)->post('/ai-manage/save-bot', [
            'name'            => 'بوت ردود المطور',
            'system_prompt'   => 'أنت مساعد احترافي جداً.',
            'welcome_message' => 'مرحباً بك!',
            'bot_tone'        => 'formal',
        ])->assertSessionHas('status');

        $bot->refresh();
        $this->assertEquals('بوت ردود المطور', $bot->name);
        $this->assertEquals('formal', $bot->bot_tone);
        $this->assertEquals('أنت مساعد احترافي جداً.', $bot->system_prompt);
    }

    /**
     * Test P3-13: Conversation status constraints ensure only valid values are stored.
     */
    public function test_conversation_status_constraint_fallback(): void
    {
        $workspace = Workspace::create(['company_name' => 'Test Co', 'status' => 'active']);
        
        $conv = Conversation::create([
            'workspace_id' => $workspace->id,
            'status'       => 'invalid_custom_status',
        ]);

        $this->assertEquals(Conversation::STATUS_OPEN, $conv->status);

        $conv->status = Conversation::STATUS_CLOSED_BY_BOT;
        $conv->save();
        $this->assertEquals(Conversation::STATUS_CLOSED_BY_BOT, $conv->status);
    }

    /**
     * Test P3-12: Webhook signature verification rejecting unsigned or invalid signatures.
     */
    public function test_webhook_signature_validation(): void
    {
        $workspace = Workspace::create([
            'company_name'   => 'Secure Store',
            'status'         => 'active',
            'webhook_secret' => 'super_secret_key_123',
        ]);

        $payload = [
            'workspace_id'  => $workspace->id,
            'customer_name' => 'عميل موثق',
            'message'       => 'رسالة مشفرة',
        ];

        // 1. Missing signature -> 401
        $response1 = $this->postJson('/api/webhook/incoming', $payload);
        $response1->assertStatus(401);

        // 2. Invalid signature -> 401
        $response2 = $this->withHeaders(['X-Webhook-Signature' => 'invalid_sig'])
                          ->postJson('/api/webhook/incoming', $payload);
        $response2->assertStatus(401);

        // 3. Valid signature -> 200
        $rawContent = json_encode($payload);
        $validSignature = hash_hmac('sha256', $rawContent, 'super_secret_key_123');

        $response3 = $this->withHeaders(['X-Webhook-Signature' => $validSignature])
                          ->postJson('/api/webhook/incoming', $payload);
        $response3->assertStatus(200)
                  ->assertJson(['success' => true]);
    }

    /**
     * Test Meta WhatsApp webhook verification handshake.
     */
    public function test_whatsapp_webhook_verification_handshake(): void
    {
        $response = $this->get('/api/webhook/whatsapp?hub_mode=subscribe&hub_verify_token=rudood_secret&hub_challenge=987654321');

        $response->assertStatus(200);
        $this->assertEquals('987654321', $response->getContent());
    }

    /**
     * Test P3-11: KnowledgeBase model chunking attribute.
     */
    public function test_knowledge_base_chunks_attribute(): void
    {
        $workspace = Workspace::create(['company_name' => 'Store', 'status' => 'active']);
        $bot = Bot::create(['workspace_id' => $workspace->id, 'name' => 'Bot', 'is_active' => true]);

        $doc = KnowledgeBase::create([
            'bot_id'        => $bot->id,
            'file_name'     => 'faq.txt',
            'file_path'     => 'faq.txt',
            'document_text' => "الفقرة الأولى: تفاصيل الشحن والتوصيل داخل المملكة.\n\nالفقرة الثانية: سياسة الدفع والاسترجاع والضمان لمدة سنتين.",
        ]);

        $chunks = $doc->chunks;
        $this->assertCount(2, $chunks);
        $this->assertStringContainsString('تفاصيل الشحن', $chunks[0]);
        $this->assertStringContainsString('سياسة الدفع', $chunks[1]);
    }

    /**
     * Test P1-5: Login rate limiting (5 attempts allowed, 6th blocked with 429).
     */
    public function test_login_rate_limiting_blocks_after_5_attempts(): void
    {
        RateLimiter::clear('login:127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email'    => 'invalid@user.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post('/login', [
            'email'    => 'invalid@user.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }

    /**
     * Test P2-10: Live chat sendMessage creates message and returns JSON.
     */
    public function test_agent_can_send_live_chat_message(): void
    {
        $workspace = Workspace::create(['company_name' => 'Store', 'status' => 'active']);
        $user = User::create([
            'name'         => 'Agent User',
            'email'        => 'agent@store.com',
            'password'     => Hash::make('password123'),
            'workspace_id' => $workspace->id,
            'role'         => 'owner',
        ]);
        $customer = Customer::create(['workspace_id' => $workspace->id, 'name' => 'العميل']);
        $conv = Conversation::create(['workspace_id' => $workspace->id, 'customer_id' => $customer->id]);

        $response = $this->actingAs($user)->postJson("/live-chat/{$conv->id}/send", [
            'content' => 'مرحباً بك، يسعدني خدمتك!',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conv->id,
            'sender_type'     => 'agent',
            'content'         => 'مرحباً بك، يسعدني خدمتك!',
        ]);
    }

    /**
     * Test P2-8: Dashboard metrics calculate real resolution rate correctly.
     */
    public function test_dashboard_resolution_rate_calculation(): void
    {
        $workspace = Workspace::create(['company_name' => 'Analytics Store', 'status' => 'active']);
        $user = User::create([
            'name'         => 'Owner',
            'email'        => 'owner_stats@store.com',
            'password'     => Hash::make('password123'),
            'workspace_id' => $workspace->id,
            'role'         => 'owner',
        ]);

        // Conversation 1: bot answered, no agent intervention -> bot resolved
        $conv1 = Conversation::create(['workspace_id' => $workspace->id]);
        $conv1->messages()->create(['sender_type' => 'customer', 'content' => 'مرحبا']);
        $conv1->messages()->create(['sender_type' => 'bot', 'content' => 'أهلا']);

        // Conversation 2: agent answered -> not bot resolved
        $conv2 = Conversation::create(['workspace_id' => $workspace->id]);
        $conv2->messages()->create(['sender_type' => 'customer', 'content' => 'مساعدة']);
        $conv2->messages()->create(['sender_type' => 'agent', 'content' => 'تفضل']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('50%'); // 1 out of 2 = 50%
    }

    /**
     * Test P5-1: Multi-turn history is properly structured and sent to AI provider.
     */
    public function test_multi_turn_history_in_ai_service(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.openai.com/v1/chat/completions' => \Illuminate\Support\Facades\Http::response([
                'choices' => [
                    ['message' => ['content' => 'الخيار الثاني هو الشحن السريع.']]
                ]
            ], 200),
        ]);

        $workspace = Workspace::create(['company_name' => 'Test Store', 'status' => 'active']);
        $bot = Bot::create([
            'workspace_id' => $workspace->id,
            'name'         => 'OpenAI Bot',
            'ai_provider'  => 'openai',
            'api_key'      => 'sk-test-key-12345',
            'model_type'   => 'gpt-4o-mini',
            'is_active'    => true,
        ]);

        $history = [
            ['sender_type' => 'customer', 'content' => 'ما هي خيارات الشحن؟'],
            ['sender_type' => 'bot',      'content' => 'لدينا خياران: عادي وسريع.'],
        ];

        $aiService = new \App\Services\AiService($bot);
        $reply = $aiService->generateReply('وما هو الخيار الثاني بالتفصيل؟', 'سياق المتجر', $history);

        $this->assertEquals('الخيار الثاني هو الشحن السريع.', $reply);

        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            $data = $request->data();
            $messages = $data['messages'];
            return count($messages) === 4
                && $messages[0]['role'] === 'system'
                && $messages[1]['role'] === 'user'
                && $messages[1]['content'] === 'ما هي خيارات الشحن؟'
                && $messages[2]['role'] === 'assistant'
                && $messages[2]['content'] === 'لدينا خياران: عادي وسريع.'
                && $messages[3]['role'] === 'user'
                && $messages[3]['content'] === 'وما هو الخيار الثاني بالتفصيل؟';
        });
    }

    /**
     * Test P4-3: ProcessCustomerMessage logs auto-rule triggers to ai_decision_logs.
     */
    public function test_process_customer_message_records_auto_rule_decision_log(): void
    {
        $workspace = Workspace::create(['company_name' => 'Decision Store', 'status' => 'active']);
        $bot = Bot::create(['workspace_id' => $workspace->id, 'name' => 'Bot', 'is_active' => true]);
        $customer = Customer::create(['workspace_id' => $workspace->id, 'name' => 'فهد']);
        $conv = Conversation::create(['workspace_id' => $workspace->id, 'customer_id' => $customer->id]);

        AutoRule::create([
            'workspace_id'     => $workspace->id,
            'question'         => 'ما هي أسعاركم؟',
            'keywords'         => ['اسعار', 'سعر', 'تكلفة'],
            'trigger_condition'=> 'contains',
            'reply_template'   => 'تبدأ باقاتنا من 99 ريال شهرياً.',
            'is_active'        => true,
        ]);

        $msg = Message::create([
            'conversation_id' => $conv->id,
            'sender_type'     => 'customer',
            'content'         => 'أريد معرفة اسعار الخدمة لديكم',
        ]);

        (new \App\Jobs\ProcessCustomerMessage($conv->id, $msg->id))->handle();

        $this->assertDatabaseHas('ai_decision_logs', [
            'conversation_id'  => $conv->id,
            'trigger'          => 'auto_rule',
            'customer_message' => 'أريد معرفة اسعار الخدمة لديكم',
            'bot_reply'        => 'تبدأ باقاتنا من 99 ريال شهرياً.',
        ]);
    }

    /**
     * Test P4-1: Web responses include Content-Security-Policy & security headers.
     */
    public function test_content_security_policy_headers_present_on_web_routes(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("cdn.socket.io", $csp);
        $this->assertStringContainsString("cdn.jsdelivr.net", $csp);
    }

    /**
     * Test P4-3 & P5-1: ProcessCustomerMessage with AI API trigger logs context and multi-turn flow.
     */
    public function test_process_customer_message_records_ai_api_decision_log_with_multi_turn(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://api.openai.com/v1/chat/completions' => \Illuminate\Support\Facades\Http::response([
                'choices' => [
                    ['message' => ['content' => 'نعم متوفر بلونين الأبيض والأسود.']]
                ]
            ], 200),
        ]);

        $workspace = Workspace::create(['company_name' => 'AI Test Store', 'status' => 'active']);
        $bot = Bot::create([
            'workspace_id' => $workspace->id,
            'name'         => 'Chat Bot',
            'ai_provider'  => 'openai',
            'api_key'      => 'sk-valid-key',
            'model_type'   => 'gpt-4o-mini',
            'is_active'    => true,
        ]);
        $customer = Customer::create(['workspace_id' => $workspace->id, 'name' => 'سارة']);
        $conv = Conversation::create(['workspace_id' => $workspace->id, 'customer_id' => $customer->id]);

        // Prior conversation turn
        Message::create([
            'conversation_id' => $conv->id,
            'sender_type'     => 'customer',
            'content'         => 'هل المنتج متوفر لديكم؟',
        ]);
        Message::create([
            'conversation_id' => $conv->id,
            'sender_type'     => 'bot',
            'content'         => 'نعم متوفر بجميع المقاسات.',
        ]);

        // Latest message
        $msg = Message::create([
            'conversation_id' => $conv->id,
            'sender_type'     => 'customer',
            'content'         => 'وما هي الألوان المتوفرة منه؟',
        ]);

        (new \App\Jobs\ProcessCustomerMessage($conv->id, $msg->id))->handle();

        $this->assertDatabaseHas('ai_decision_logs', [
            'conversation_id'  => $conv->id,
            'trigger'          => 'ai_api',
            'ai_provider'      => 'openai',
            'model_type'       => 'gpt-4o-mini',
            'customer_message' => 'وما هي الألوان المتوفرة منه؟',
            'bot_reply'        => 'نعم متوفر بلونين الأبيض والأسود.',
        ]);

        $log = AiDecisionLog::where('conversation_id', $conv->id)->where('trigger', 'ai_api')->first();
        $this->assertNotNull($log);
        $this->assertGreaterThanOrEqual(1, $log->response_time_ms);
    }
}
