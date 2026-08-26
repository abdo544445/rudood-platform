<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AutoRule;
use App\Models\KnowledgeBase;
use App\Jobs\ProcessCustomerMessage;
use App\Services\RagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Crypt;

class ChannelsAndPlaygroundTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Workspace $workspace;
    private Bot $bot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::create([
            'company_name'   => 'متجر التجربة',
            'status'         => 'active',
            'webhook_secret' => 'test_webhook_secret',
        ]);

        $this->user = User::create([
            'name'         => 'مالك المتجر',
            'email'        => 'owner@store.com',
            'password'     => bcrypt('password123'),
            'role'         => 'owner',
            'workspace_id' => $this->workspace->id,
        ]);

        $this->bot = Bot::create([
            'workspace_id'    => $this->workspace->id,
            'name'            => 'المساعد الذكي',
            'is_active'       => true,
            'ai_provider'     => 'gemini',
            'model_type'      => 'gemini-1.5-flash',
            'api_key'         => 'fake_key_123',
            'welcome_message' => 'مرحباً بك!',
        ]);
    }

    /**
     * 1. Test Channel tokens are encrypted at rest in DB
     */
    public function test_channel_tokens_are_stored_encrypted_in_db()
    {
        $channel = Channel::create([
            'workspace_id'    => $this->workspace->id,
            'platform'        => 'whatsapp',
            'access_token'    => 'secret_wa_token_123',
            'verify_token'    => 'secret_verify_token_456',
            'phone_number_id' => '1029384756',
        ]);

        // When retrieved through Eloquent, it decrypts
        $this->assertEquals('secret_wa_token_123', $channel->access_token);
        $this->assertEquals('secret_verify_token_456', $channel->verify_token);

        // In raw DB, it is not plain text
        $raw = \DB::table('channels')->where('id', $channel->id)->first();
        $this->assertNotEquals('secret_wa_token_123', $raw->access_token);
        $this->assertNotEquals('secret_verify_token_456', $raw->verify_token);
    }

    /**
     * 2. Test Channel multi-tenant isolation
     */
    public function test_channel_multi_tenant_isolation()
    {
        $otherWorkspace = Workspace::create(['company_name' => 'متجر آخر', 'status' => 'active']);
        $otherChannel = Channel::create([
            'workspace_id' => $otherWorkspace->id,
            'platform'     => 'telegram',
            'bot_token'    => 'other_bot_token',
        ]);

        $response = $this->actingAs($this->user)->post("/settings/channels/disconnect/{$otherChannel->id}");
        $response->assertStatus(404);
    }

    /**
     * 3. Test Telegram Connect & Verify flow
     */
    public function test_telegram_channel_connect_and_verify()
    {
        Http::fake([
            'https://api.telegram.org/bot123456:TEST_TOKEN/getMe' => Http::response([
                'ok'     => true,
                'result' => [
                    'id'         => 123456,
                    'is_bot'     => true,
                    'first_name' => 'Rudood Test Bot',
                    'username'   => 'RudoodTestBot',
                ],
            ], 200),
            'https://api.telegram.org/bot123456:TEST_TOKEN/setWebhook' => Http::response([
                'ok'          => true,
                'result'      => true,
                'description' => 'Webhook was set',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->post('/settings/channels/connect', [
            'platform'  => 'telegram',
            'bot_token' => '123456:TEST_TOKEN',
        ]);

        $response->assertSessionHas('status');

        $channel = Channel::where('workspace_id', $this->workspace->id)
            ->where('platform', 'telegram')
            ->first();

        $this->assertNotNull($channel);
        $this->assertTrue($channel->is_connected);
        $this->assertEquals('RudoodTestBot', $channel->bot_username);
    }

    /**
     * 4. Test WhatsApp Connect & Verify flow
     */
    public function test_whatsapp_channel_connect_and_verify()
    {
        Http::fake([
            'https://graph.facebook.com/v19.0/100020003000' => Http::response([
                'display_phone_number' => '+966500001234',
                'id'                   => '100020003000',
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->post('/settings/channels/connect', [
            'platform'        => 'whatsapp',
            'access_token'    => 'EAAB_VALID_TOKEN',
            'phone_number_id' => '100020003000',
            'verify_token'    => 'my_custom_verify_token',
        ]);

        $response->assertSessionHas('status');

        $channel = Channel::where('workspace_id', $this->workspace->id)
            ->where('platform', 'whatsapp')
            ->first();

        $this->assertNotNull($channel);
        $this->assertTrue($channel->is_connected);
    }

    /**
     * 5. Test WhatsApp Handshake verifies against channel custom token
     */
    public function test_whatsapp_handshake_with_channel_verify_token()
    {
        Channel::create([
            'workspace_id'    => $this->workspace->id,
            'platform'        => 'whatsapp',
            'access_token'    => 'token',
            'phone_number_id' => 'phone123',
            'verify_token'    => 'store_custom_secret',
        ]);

        $response = $this->get('/api/webhook/whatsapp?hub_mode=subscribe&hub_verify_token=store_custom_secret&hub_challenge=CHALLENGE_ACCEPTED');

        $response->assertStatus(200);
        $this->assertEquals('CHALLENGE_ACCEPTED', $response->getContent());
    }

    /**
     * 6. Test Incoming Telegram Webhook
     */
    public function test_incoming_telegram_webhook_creates_customer_and_dispatches_job()
    {
        Queue::fake();

        $payload = [
            'update_id' => 999123,
            'message'   => [
                'message_id' => 45,
                'from'       => [
                    'id'         => 888777,
                    'first_name' => 'سارة',
                    'last_name'  => 'أحمد',
                    'username'   => 'sara_ahmed',
                ],
                'chat' => [
                    'id'   => 888777,
                    'type' => 'private',
                ],
                'text' => 'مرحباً، هل يتوفر لديكم مقاس لارج؟',
            ],
        ];

        $response = $this->postJson("/api/webhook/telegram/{$this->workspace->id}", $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        $customer = Customer::where('workspace_id', $this->workspace->id)
            ->where('chat_id', '888777')
            ->first();

        $this->assertNotNull($customer);
        $this->assertEquals('سارة أحمد', $customer->name);
        $this->assertEquals('telegram', $customer->platform);

        $conversation = Conversation::where('workspace_id', $this->workspace->id)
            ->where('customer_id', $customer->id)
            ->first();

        $this->assertNotNull($conversation);

        $msg = Message::where('conversation_id', $conversation->id)->first();
        $this->assertNotNull($msg);
        $this->assertEquals('مرحباً، هل يتوفر لديكم مقاس لارج؟', $msg->content);

        Queue::assertPushed(ProcessCustomerMessage::class);
    }

    /**
     * 7. Test ProcessCustomerMessage dispatches outgoing WhatsApp & Telegram API calls
     */
    public function test_process_customer_message_sends_outgoing_replies()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'نعم، المقاس متوفر ويمكنك طلبه الآن!']]]]
                ]
            ], 200),
        ]);

        Channel::create([
            'workspace_id' => $this->workspace->id,
            'platform'     => 'telegram',
            'bot_token'    => 'valid_telegram_token',
            'is_connected' => true,
        ]);

        $customer = Customer::create([
            'workspace_id' => $this->workspace->id,
            'name'         => 'أحمد',
            'chat_id'      => '998877',
            'platform'     => 'telegram',
        ]);

        $conversation = Conversation::create([
            'workspace_id' => $this->workspace->id,
            'customer_id'  => $customer->id,
            'status'       => 'open',
        ]);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'customer',
            'content'         => 'هل المقاس متوفر؟',
        ]);

        $ragService = new RagService();
        $job = new ProcessCustomerMessage($conversation->id, $msg->id);
        $job->handle($ragService);

        // Assert bot message created
        $botMsg = Message::where('conversation_id', $conversation->id)
            ->where('sender_type', 'bot')
            ->first();

        $this->assertNotNull($botMsg);

        // Assert Telegram outgoing request was sent
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/botvalid_telegram_token/sendMessage')
                && $request['chat_id'] === '998877';
        });
    }

    /**
     * 8. Test AI Playground Console with FAQ rule & RAG knowledge chunk
     */
    public function test_ai_playground_console_triggers_auto_rule_and_rag()
    {
        // 1. Auto Rule test
        AutoRule::create([
            'workspace_id'     => $this->workspace->id,
            'question'         => 'ما هي سياسة الاسترجاع؟',
            'keywords'         => ['استرجاع', 'ارجاع', 'تبديل'],
            'reply_template'   => 'يمكن الاسترجاع خلال 14 يوماً من الاستلام.',
            'is_active'        => true,
        ]);

        $response = $this->actingAs($this->user)->post('/ai-manage/test', [
            'question' => 'أريد استرجاع المنتج',
        ]);

        $response->assertSessionHas('playground');
        $pg = session('playground');
        $this->assertEquals('auto_rule', $pg['trigger']);
        $this->assertEquals('يمكن الاسترجاع خلال 14 يوماً من الاستلام.', $pg['reply']);

        // 2. RAG Document test
        KnowledgeBase::create([
            'bot_id'        => $this->bot->id,
            'file_name'     => 'catalog.pdf',
            'file_path'     => 'knowledge/1/catalog.pdf',
            'document_text' => 'مواعيد العمل الرسمية من الساعة 9 صباحاً حتى 10 مساءً طوال أيام الأسبوع.',
            'status'        => 'processed',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'نعمل من 9 صباحاً حتى 10 مساءً.']]]]
                ]
            ], 200),
        ]);

        $response2 = $this->actingAs($this->user)->post('/ai-manage/test', [
            'question' => 'ما هي مواعيد وساعات العمل الرسمية لديكم؟',
        ]);

        $response2->assertSessionHas('playground');
        $pg2 = session('playground');
        $this->assertEquals('ai_api', $pg2['trigger']);
        $this->assertNotEmpty($pg2['chunks']);
        $this->assertGreaterThan(0, $pg2['chunks'][0]['score']);
    }
}
