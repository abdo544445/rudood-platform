<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AiDecisionLog;
use App\Services\AdminStatsService;
use Illuminate\Support\Facades\Hash;

class AdminStatisticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test super admin can access statistics portal.
     */
    public function test_super_admin_can_access_statistics_portal(): void
    {
        $workspace = Workspace::create(['company_name' => 'HQ', 'status' => 'active']);
        $admin = User::create([
            'name'         => 'Super Admin',
            'email'        => 'admin@rudood.com',
            'password'     => Hash::make('password123'),
            'workspace_id' => $workspace->id,
            'role'         => 'super_admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/statistics');

        $response->assertStatus(200);
        $response->assertSee('لوحة الإحصائيات الشاملة');
        $response->assertSee('مركز المراقبة والتحليلات الشاملة');
        $response->assertSee('أداء ومراقبة أسطول الشركات');
    }

    /**
     * Test regular store owner is blocked from accessing admin statistics.
     */
    public function test_regular_owner_cannot_access_admin_statistics(): void
    {
        $workspace = Workspace::create(['company_name' => 'Store Co', 'status' => 'active']);
        $owner = User::create([
            'name'         => 'Regular Owner',
            'email'        => 'owner@store.com',
            'password'     => Hash::make('password123'),
            'workspace_id' => $workspace->id,
            'role'         => 'owner',
        ]);

        $response = $this->actingAs($owner)->get('/admin/statistics');
        $response->assertStatus(403);
    }

    /**
     * Test AdminStatsService returns accurate aggregate metrics and daily series.
     */
    public function test_admin_stats_service_aggregates(): void
    {
        $workspace = Workspace::create(['company_name' => 'Analytics Co', 'status' => 'active']);
        $bot = Bot::create([
            'workspace_id' => $workspace->id,
            'name'         => 'Bot 1',
            'ai_provider'  => 'gemini',
            'is_active'    => true,
        ]);
        $conv = Conversation::create(['workspace_id' => $workspace->id]);
        $customerMsg = Message::create([
            'conversation_id' => $conv->id,
            'sender_type'     => 'customer',
            'content'         => 'سؤال تجريبي',
        ]);
        $botMsg = Message::create([
            'conversation_id' => $conv->id,
            'sender_type'     => 'bot',
            'content'         => 'جواب البوت',
        ]);

        AiDecisionLog::create([
            'conversation_id'  => $conv->id,
            'message_id'       => $botMsg->id,
            'trigger'          => 'ai_api',
            'ai_provider'      => 'gemini',
            'model_type'       => 'gemini-1.5-flash',
            'customer_message' => $customerMsg->content,
            'bot_reply'        => $botMsg->content,
            'response_time_ms' => 120,
        ]);

        $service = app(AdminStatsService::class);
        $global = $service->globalStats();

        $this->assertEquals(1, $global['total_workspaces']);
        $this->assertEquals(1, $global['active_workspaces']);
        $this->assertEquals(1, $global['total_bots']);
        $this->assertEquals(1, $global['total_conversations']);
        $this->assertEquals(2, $global['total_messages']);
        $this->assertEquals(1, $global['bot_messages']);
        $this->assertEquals(50.0, $global['global_resolution_rate']);

        $dailyMsgs = $service->dailyMessages(14);
        $this->assertCount(14, $dailyMsgs['labels']);
        $this->assertCount(14, $dailyMsgs['bot']);
        $this->assertCount(14, $dailyMsgs['human']);

        $aiStats = $service->aiDecisionStats();
        $this->assertEquals(1, $aiStats['total_decisions']);
        $this->assertEquals(1, $aiStats['by_trigger']['ai_api']);

        $wsTable = $service->workspaceTable();
        $this->assertCount(1, $wsTable);
        $this->assertEquals('Analytics Co', $wsTable->first()['company_name']);
        $this->assertEquals(50.0, $wsTable->first()['resolution_rate']);
    }

    /**
     * Test AdminSystemController works without throwing on SQLite or MySQL.
     */
    public function test_admin_system_page_renders_cleanly(): void
    {
        $workspace = Workspace::create(['company_name' => 'HQ', 'status' => 'active']);
        $admin = User::create([
            'name'         => 'Super Admin',
            'email'        => 'admin2@rudood.com',
            'password'     => Hash::make('password123'),
            'workspace_id' => $workspace->id,
            'role'         => 'super_admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/system');
        $response->assertStatus(200);
        $response->assertSee('حالة النظام والخدمات');
        $response->assertDontSee('خطأ بالاتصال:');
    }
}
