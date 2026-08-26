<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\KnowledgeBase;
use App\Models\AutoRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. High-Level KPI Statistics
        $total_workspaces = Workspace::count();
        $active_workspaces = Workspace::where('status', 'active')->count();
        $new_workspaces_this_month = Workspace::whereMonth('created_at', now()->month)->count();

        $total_users = User::count();
        $total_bots = Bot::count();
        $active_bots = Bot::where('is_active', true)->count();

        $total_conversations = Conversation::count();
        $total_messages = Message::count();
        $bot_messages = Message::where('sender_type', 'bot')->count();
        $human_messages = Message::whereIn('sender_type', ['customer', 'user'])->count();

        $global_resolution_rate = $total_messages > 0 ? round(($bot_messages / $total_messages) * 100, 1) : 0;

        $total_knowledge_docs = KnowledgeBase::count();
        $total_auto_rules = AutoRule::count();

        // 2. SaaS & Financial Metrics
        $total_subscriptions = Subscription::count();
        $active_subscriptions = Subscription::where('status', 'active')->count();
        $estimated_mrr = Subscription::where('status', 'active')->sum('price');
        $estimated_arr = $estimated_mrr * 12;

        // 3. AI Providers Fleet Breakdown
        $ai_providers_breakdown = Bot::select('ai_provider', DB::raw('count(*) as count'))
            ->groupBy('ai_provider')
            ->pluck('count', 'ai_provider')
            ->toArray();

        // Provide defaults if empty
        $provider_stats = [
            'gemini'            => $ai_providers_breakdown['gemini'] ?? 0,
            'openai'            => $ai_providers_breakdown['openai'] ?? 0,
            'anthropic'         => $ai_providers_breakdown['anthropic'] ?? 0,
            'openai_compatible' => $ai_providers_breakdown['openai_compatible'] ?? 0,
        ];

        // 4. Daily Messages Timeline (Last 7 Days)
        $daily_labels = [];
        $daily_bot_data = [];
        $daily_human_data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $daily_labels[] = $date->format('Y-m-d');

            $daily_bot_data[] = Message::where('sender_type', 'bot')
                ->whereDate('created_at', $date->toDateString())
                ->count();

            $daily_human_data[] = Message::whereIn('sender_type', ['customer', 'user'])
                ->whereDate('created_at', $date->toDateString())
                ->count();
        }

        // 5. Recent Activity
        $recent_workspaces = Workspace::withCount(['users', 'bots', 'conversations'])
            ->latest()
            ->take(6)
            ->get();

        $recent_users = User::with('workspace')
            ->latest()
            ->take(6)
            ->get();

        // 6. Quick Infrastructure Status
        $system_health = $this->getQuickSystemHealth();

        $stats = [
            'total_workspaces'          => $total_workspaces,
            'active_workspaces'         => $active_workspaces,
            'new_workspaces_this_month' => $new_workspaces_this_month,
            'total_users'               => $total_users,
            'total_bots'                => $total_bots,
            'active_bots'               => $active_bots,
            'total_conversations'       => $total_conversations,
            'total_messages'            => $total_messages,
            'bot_messages'              => $bot_messages,
            'human_messages'            => $human_messages,
            'global_resolution_rate'    => $global_resolution_rate,
            'total_knowledge_docs'      => $total_knowledge_docs,
            'total_auto_rules'          => $total_auto_rules,
            'active_subscriptions'      => $active_subscriptions,
            'estimated_mrr'             => $estimated_mrr,
            'estimated_arr'             => $estimated_arr,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'provider_stats',
            'daily_labels',
            'daily_bot_data',
            'daily_human_data',
            'recent_workspaces',
            'recent_users',
            'system_health'
        ));
    }

    /**
     * Check infrastructure status for health summary widget
     */
    private function getQuickSystemHealth(): array
    {
        $db_connected = false;
        try {
            DB::connection()->getPdo();
            $db_connected = true;
        } catch (\Throwable $e) {}

        $redis_connected = false;
        try {
            Redis::ping();
            $redis_connected = true;
        } catch (\Throwable $e) {}

        $ws_connected = false;
        try {
            $response = Http::timeout(2)->get(config('services.websocket_url', 'http://localhost:3000') . '/health');
            $ws_connected = $response->successful();
        } catch (\Throwable $e) {
            // Check if socket responds on 3000 port locally
            try {
                $response = Http::timeout(2)->get('http://127.0.0.1:3000');
                $ws_connected = true;
            } catch (\Throwable $ex) {}
        }

        return [
            'database'  => $db_connected,
            'redis'     => $redis_connected,
            'websocket' => $ws_connected,
        ];
    }
}
