<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\KnowledgeBase;
use App\Models\AutoRule;
use App\Models\AiDecisionLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

class AdminStatsService
{
    /**
     * 1. Core global KPIs
     */
    public function globalStats(): array
    {
        $total_workspaces     = Workspace::count();
        $active_workspaces    = Workspace::where('status', 'active')->count();
        $suspended_workspaces = Workspace::where('status', 'suspended')->count();
        $trial_workspaces     = Workspace::where('status', 'trial')->count();

        $total_users = User::count();
        $total_bots  = Bot::count();
        $active_bots = Bot::where('is_active', true)->count();

        $total_conversations = Conversation::count();
        $total_messages      = Message::count();
        $bot_messages        = Message::where('sender_type', 'bot')->count();
        $human_messages      = Message::whereIn('sender_type', ['customer', 'user', 'agent'])->count();

        $global_resolution_rate = $total_messages > 0
            ? round(($bot_messages / $total_messages) * 100, 1)
            : 0;

        $total_knowledge_docs = KnowledgeBase::count();
        $total_auto_rules     = AutoRule::count();

        $active_subscriptions = Subscription::where('status', 'active')->count();
        $estimated_mrr        = (float) Subscription::where('status', 'active')->sum('price');
        $estimated_arr        = $estimated_mrr * 12;

        return [
            'total_workspaces'       => $total_workspaces,
            'active_workspaces'      => $active_workspaces,
            'suspended_workspaces'   => $suspended_workspaces,
            'trial_workspaces'       => $trial_workspaces,
            'total_users'            => $total_users,
            'total_bots'             => $total_bots,
            'active_bots'            => $active_bots,
            'total_conversations'    => $total_conversations,
            'total_messages'         => $total_messages,
            'bot_messages'           => $bot_messages,
            'human_messages'         => $human_messages,
            'global_resolution_rate' => $global_resolution_rate,
            'total_knowledge_docs'   => $total_knowledge_docs,
            'total_auto_rules'       => $total_auto_rules,
            'active_subscriptions'   => $active_subscriptions,
            'estimated_mrr'          => $estimated_mrr,
            'estimated_arr'          => $estimated_arr,
        ];
    }

    /**
     * 2. Conversation growth timeline (last N days)
     */
    public function dailyConversations(int $days = 14): array
    {
        $labels = [];
        $data   = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('Y-m-d');
            $data[]   = Conversation::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    /**
     * 3. Message volume timeline (last N days) — bot vs human
     */
    public function dailyMessages(int $days = 14): array
    {
        $labels = [];
        $bot    = [];
        $human  = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('Y-m-d');

            $bot[] = Message::where('sender_type', 'bot')
                ->whereDate('created_at', $date->toDateString())
                ->count();

            $human[] = Message::whereIn('sender_type', ['customer', 'user', 'agent'])
                ->whereDate('created_at', $date->toDateString())
                ->count();
        }

        return [
            'labels' => $labels,
            'bot'    => $bot,
            'human'  => $human,
        ];
    }

    /**
     * 4. New workspaces created per day (last N days)
     */
    public function dailyNewWorkspaces(int $days = 14): array
    {
        $labels = [];
        $data   = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('Y-m-d');
            $data[]   = Workspace::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    /**
     * 5. AI provider fleet breakdown
     */
    public function aiProviderBreakdown(): array
    {
        $counts = Bot::select('ai_provider', DB::raw('count(*) as aggregate'))
            ->groupBy('ai_provider')
            ->pluck('aggregate', 'ai_provider')
            ->toArray();

        return [
            'gemini'            => $counts['gemini'] ?? 0,
            'openai'            => $counts['openai'] ?? 0,
            'anthropic'         => $counts['anthropic'] ?? 0,
            'openai_compatible' => $counts['openai_compatible'] ?? 0,
        ];
    }

    /**
     * 6. AI decision log aggregates
     */
    public function aiDecisionStats(): array
    {
        $totalDecisions = 0;
        $byTrigger = [
            'auto_rule' => 0,
            'ai_api'    => 0,
            'fallback'  => 0,
        ];
        $recentDecisions = collect();

        try {
            if (class_exists(AiDecisionLog::class)) {
                $totalDecisions = AiDecisionLog::count();
                $triggers = AiDecisionLog::select('trigger', DB::raw('count(*) as count'))
                    ->groupBy('trigger')
                    ->pluck('count', 'trigger')
                    ->toArray();

                $byTrigger['auto_rule'] = $triggers['auto_rule'] ?? 0;
                $byTrigger['ai_api']    = $triggers['ai_api'] ?? 0;
                $byTrigger['fallback']  = $triggers['fallback'] ?? 0;

                $recentDecisions = AiDecisionLog::with(['conversation'])
                    ->latest()
                    ->take(10)
                    ->get();
            }
        } catch (\Throwable $e) {
            // Graceful fallback if table does not exist yet
        }

        return [
            'total_decisions'  => $totalDecisions,
            'by_trigger'       => $byTrigger,
            'recent_decisions' => $recentDecisions,
        ];
    }

    /**
     * 7. Queue & failed job monitoring
     */
    public function queueStats(): array
    {
        $pending = 0;
        $failed  = 0;

        try {
            $pending = DB::table('jobs')->count();
            $failed  = DB::table('failed_jobs')->count();
        } catch (\Throwable $e) {}

        return [
            'pending_jobs' => $pending,
            'failed_jobs'  => $failed,
        ];
    }

    /**
     * 8. Per-workspace monitoring table
     */
    public function workspaceTable(): Collection
    {
        return Workspace::with(['bots', 'subscription'])
            ->withCount(['users', 'bots', 'conversations'])
            ->get()
            ->map(function ($ws) {
                $convIds = Conversation::where('workspace_id', $ws->id)->pluck('id');
                $totalMsgs = Message::whereIn('conversation_id', $convIds)->count();
                $botMsgs   = Message::whereIn('conversation_id', $convIds)->where('sender_type', 'bot')->count();

                $resolutionRate = $totalMsgs > 0 ? round(($botMsgs / $totalMsgs) * 100, 1) : 0;
                $activeBot = $ws->bots->firstWhere('is_active', true) ?? $ws->bots->first();
                $lastMessage = Message::whereIn('conversation_id', $convIds)->latest()->first();

                return [
                    'id'                 => $ws->id,
                    'company_name'       => $ws->company_name,
                    'status'             => $ws->status ?? 'active',
                    'users_count'        => $ws->users_count,
                    'bots_count'         => $ws->bots_count,
                    'conversations_count'=> $ws->conversations_count,
                    'messages_count'     => $totalMsgs,
                    'resolution_rate'    => $resolutionRate,
                    'ai_provider'        => $activeBot?->ai_provider ?? 'gemini',
                    'model_type'         => $activeBot?->model_type ?? 'gemini-1.5-flash',
                    'plan'               => $ws->subscription?->plan_name ?? $ws->subscription?->plan ?? 'Standard',
                    'last_activity'      => $lastMessage?->created_at?->diffForHumans() ?? 'لا يوجد نشاط',
                ];
            });
    }

    /**
     * 9. System health status
     */
    public function systemHealth(): array
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
            try {
                $response = Http::timeout(2)->get('http://127.0.0.1:3000/health');
                $ws_connected = $response->successful();
            } catch (\Throwable $ex) {}
        }

        return [
            'database'  => $db_connected,
            'redis'     => $redis_connected,
            'websocket' => $ws_connected,
        ];
    }

    /**
     * A.2.1 Revenue & Subscriptions Telemetry
     */
    public function subscriptionStats(): array
    {
        $active_subscriptions = Subscription::where('status', 'active')->count();
        $trial_subscriptions = Subscription::where('status', 'trial')->count();
        $estimated_mrr = (float) Subscription::where('status', 'active')->sum('price');
        $estimated_arr = $estimated_mrr * 12;

        $breakdown = Subscription::with('workspace')
            ->orderBy('price', 'desc')
            ->take(10)
            ->get();

        return [
            'active_subscriptions' => $active_subscriptions,
            'trial_count'          => $trial_subscriptions,
            'estimated_mrr'        => $estimated_mrr,
            'estimated_arr'        => $estimated_arr,
            'breakdown'            => $breakdown,
        ];
    }

    /**
     * A.2.2 Real-time Live Activity Feed
     */
    public function recentLiveActivity(int $minutes = 15): Collection
    {
        return Message::with('conversation.customer')
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->whereIn('sender_type', ['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
    }

    /**
     * A.2.3 Error & Failed-Job Analysis (list)
     */
    public function failedJobs(): Collection
    {
        try {
            return DB::table('failed_jobs')->orderBy('failed_at', 'desc')->take(20)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * A.2.3 Prune Failed Jobs
     */
    public function pruneFailedJobs(): int
    {
        try {
            $count = DB::table('failed_jobs')->count();
            DB::table('failed_jobs')->truncate();
            return $count;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * A.2.4 Daily Error / Activity Trend chart
     */
    public function dailyOperations(int $days = 14): array
    {
        $labels = [];
        $processed = [];
        $failures = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('Y-m-d');
            
            $processed[] = 0; // Requires a history table

            try {
                $failures[] = DB::table('failed_jobs')
                    ->whereDate('failed_at', $date->toDateString())
                    ->count();
            } catch (\Throwable $e) {
                $failures[] = 0;
            }
        }

        return [
            'labels'    => $labels,
            'processed' => $processed,
            'failures'  => $failures,
        ];
    }

    /**
     * A.2.5 Knowledge Base & Documents Health
     */
    public function knowledgeHealth(): array
    {
        $totalDocs = KnowledgeBase::count();
        $totalRules = AutoRule::count();
        
        $orphanedDocs = KnowledgeBase::whereNull('bot_id')
            ->orWhereNotIn('bot_id', function ($query) {
                $query->select('id')->from('bots');
            })->count();

        $docsPerWorkspace = DB::table('knowledge_bases')
            ->join('bots', 'knowledge_bases.bot_id', '=', 'bots.id')
            ->join('workspaces', 'bots.workspace_id', '=', 'workspaces.id')
            ->select('workspaces.id as workspace_id', 'workspaces.company_name', DB::raw('count(knowledge_bases.id) as count'))
            ->groupBy('workspaces.id', 'workspaces.company_name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        return [
            'total_docs'         => $totalDocs,
            'total_rules'        => $totalRules,
            'orphaned_docs'      => $orphanedDocs,
            'docs_per_workspace' => $docsPerWorkspace,
        ];
    }

    /**
     * A.2.6 Channel Connectivity Matrix
     */
    public function channelConnectivity(): array
    {
        try {
            $channels = DB::table('channels')
                ->join('workspaces', 'channels.workspace_id', '=', 'workspaces.id')
                ->select('workspaces.company_name', 'channels.platform', 'channels.is_connected', 'channels.last_error')
                ->orderBy('workspaces.company_name')
                ->get();
            return $channels->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * A.2.7 AI Provider & Cost Exposure
     */
    public function providerUsage(): array
    {
        try {
            return AiDecisionLog::select('ai_provider', DB::raw('count(*) as count'))
                ->groupBy('ai_provider')
                ->pluck('count', 'ai_provider')
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
