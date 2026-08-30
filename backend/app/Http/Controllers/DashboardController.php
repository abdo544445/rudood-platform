<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Bot;
use App\Models\User;
use App\Models\KnowledgeBase;
use App\Models\AutoRule;
use App\Models\AiDecisionLog;
use App\Models\MockOrder;
use App\Services\ConversionTrackingService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $workspace_id = auth()->user()->workspace_id;
        
        // --- 1. Top KPI Cards (Existing) ---
        $total_conversations = Conversation::where('workspace_id', $workspace_id)->count();
        $total_messages = Message::whereHas('conversation', fn($q) => $q->where('workspace_id', $workspace_id))->count();
        
        $botResolved = Conversation::where('workspace_id', $workspace_id)
            ->where(function($q) {
                $q->where('status', 'closed_by_bot')
                  ->orWhere(function($sub) {
                      $sub->whereHas('messages', fn($m) => $m->where('sender_type', 'bot'))
                          ->whereDoesntHave('messages', fn($m) => $m->where('sender_type', 'agent'));
                  });
            })
            ->count();

        $resolution_rate = $total_conversations > 0 ? round(($botResolved / $total_conversations) * 100) : 0;
        $new_inquiries = Conversation::where('workspace_id', $workspace_id)->where('status', 'open')->count();

        // Calculate average response time
        $avgResponseSeconds = null;
        try {
            $driver = \Illuminate\Support\Facades\DB::getDriverName();
            if ($driver === 'sqlite') {
                $avgResponseSeconds = Message::where('sender_type', 'bot')
                    ->whereHas('conversation', fn($q) => $q->where('workspace_id', $workspace_id))
                    ->selectRaw('AVG((STRFTIME("%s", messages.created_at) - STRFTIME("%s",
                        (SELECT created_at FROM messages AS m2
                         WHERE m2.conversation_id = messages.conversation_id
                         AND m2.sender_type = "customer"
                         AND m2.created_at < messages.created_at
                         ORDER BY m2.created_at DESC LIMIT 1)))) as avg_seconds')
                    ->value('avg_seconds');
            } elseif ($driver === 'pgsql') {
                $avgResponseSeconds = Message::where('sender_type', 'bot')
                    ->whereHas('conversation', fn($q) => $q->where('workspace_id', $workspace_id))
                    ->selectRaw('AVG(EXTRACT(EPOCH FROM (messages.created_at -
                        (SELECT created_at FROM messages AS m2
                         WHERE m2.conversation_id = messages.conversation_id
                         AND m2.sender_type = \'customer\'
                         AND m2.created_at < messages.created_at
                         ORDER BY m2.created_at DESC LIMIT 1)))) as avg_seconds')
                    ->value('avg_seconds');
            } else {
                $avgResponseSeconds = Message::where('sender_type', 'bot')
                    ->whereHas('conversation', fn($q) => $q->where('workspace_id', $workspace_id))
                    ->selectRaw('AVG(TIMESTAMPDIFF(SECOND,
                        (SELECT created_at FROM messages AS m2
                         WHERE m2.conversation_id = messages.conversation_id
                         AND m2.sender_type = "customer"
                         AND m2.created_at < messages.created_at
                         ORDER BY m2.created_at DESC LIMIT 1),
                        messages.created_at)) as avg_seconds')
                    ->value('avg_seconds');
            }
        } catch (\Throwable $e) {
            $avgResponseSeconds = null;
        }

        $formattedAvgTime = ($avgResponseSeconds !== null && $avgResponseSeconds > 0)
            ? round($avgResponseSeconds, 1) . ' ثانية'
            : ($total_messages > 0 ? 'أقل من ثانيتين' : '—');

        $stats = [
            'total_conversations' => $total_conversations,
            'resolution_rate'     => $resolution_rate . '%',
            'new_inquiries'       => $new_inquiries,
            'avg_response_time'   => $formattedAvgTime,
        ];

        // --- 2. New Secondary Cards ---
        $active_bots = Bot::where('workspace_id', $workspace_id)->where('is_active', true)->count();
        $team_users = User::where('workspace_id', $workspace_id)->count();
        $knowledge_docs = KnowledgeBase::whereHas('bot', fn($q) => $q->where('workspace_id', $workspace_id))->count();
        
        $connected_channels = 0;
        $channels = collect();
        try {
            $channels = DB::table('channels')->where('workspace_id', $workspace_id)->get();
            $connected_channels = $channels->where('is_connected', true)->count();
        } catch (\Throwable $e) {}

        $secondary_stats = [
            'active_bots'        => $active_bots,
            'team_users'         => $team_users,
            'knowledge_docs'     => $knowledge_docs,
            'connected_channels' => $connected_channels,
        ];

        // --- 3. Charts Data (7 days) ---
        $chart_labels = [];
        $chart_messages = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chart_labels[] = $date->format('m-d');
            
            $msg_count = Message::whereHas('conversation', fn($q) => $q->where('workspace_id', $workspace_id))
                ->whereDate('created_at', $date->toDateString())
                ->count();
            $chart_messages[] = $msg_count;
        }

        $channel_donut = [];
        if ($channels->count() > 0) {
            foreach ($channels as $ch) {
                $channel_donut[$ch->platform] = ($channel_donut[$ch->platform] ?? 0) + 1;
            }
        } else {
            // Default demo if no channels
            $channel_donut = ['web' => 1];
        }

        // --- 4. Tables Data ---
        $recent_conversations = Conversation::with('customer')
            ->where('workspace_id', $workspace_id)
            ->latest()
            ->take(5)
            ->get();
            
        $recent_rules = AutoRule::where('workspace_id', $workspace_id)
            ->latest()
            ->take(5)
            ->get();
            
        $recent_decisions = collect();
        try {
            if (class_exists(AiDecisionLog::class)) {
                $recent_decisions = AiDecisionLog::whereHas('conversation', fn($q) => $q->where('workspace_id', $workspace_id))
                    ->latest()
                    ->take(5)
                    ->get();
            }
        } catch (\Throwable $e) {}

        // --- 5. Conversion Analytics & ROI Tracking ---
        $conversionService = app(ConversionTrackingService::class);
        $roi_stats = $conversionService->calculateMerchantRoi($workspace_id);
        $monthly_trends = $conversionService->getMonthlyDeflectionTrends($workspace_id);
        $recent_conversions = MockOrder::where('workspace_id', $workspace_id)
            ->where('is_attributed_to_bot', true)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'secondary_stats',
            'chart_labels',
            'chart_messages',
            'channel_donut',
            'recent_conversations',
            'channels',
            'recent_rules',
            'recent_decisions',
            'roi_stats',
            'monthly_trends',
            'recent_conversions'
        ));
    }

    /**
     * Return dynamic ROI and deflection metrics (7d, 30d, 90d, 12m).
     */
    public function getRoiAnalytics(Request $request)
    {
        $workspaceId = auth()->user()->workspace_id ?? 1;
        $period = $request->query('period', '30d');

        $conversionService = app(ConversionTrackingService::class);
        $roiStats = $conversionService->calculateMerchantRoi($workspaceId, $period);
        $monthlyTrends = $conversionService->getMonthlyDeflectionTrends($workspaceId);

        return response()->json([
            'success'        => true,
            'period'         => $period,
            'roi_stats'      => $roiStats,
            'monthly_trends' => $monthlyTrends,
        ]);
    }

    public function getStats()
    {
        $workspaceId = auth()->user()->workspace_id ?? 1;

        $totalConversations = Conversation::where('workspace_id', $workspaceId)->count();
        $totalMessages = Message::whereHas('conversation', fn($q) => $q->where('workspace_id', $workspaceId))->count();
        $botMessages = Message::where('sender_type', 'bot')
            ->whereHas('conversation', fn($q) => $q->where('workspace_id', $workspaceId))->count();

        $botResolved = Conversation::where('workspace_id', $workspaceId)
            ->where(function($q) {
                $q->where('status', 'closed_by_bot')
                  ->orWhere(function($sub) {
                      $sub->whereHas('messages', fn($m) => $m->where('sender_type', 'bot'))
                          ->whereDoesntHave('messages', fn($m) => $m->where('sender_type', 'agent'));
                  });
            })
            ->count();

        $resolutionRate = $totalConversations > 0 ? round(($botResolved / $totalConversations) * 100) : 0;
        $newInquiries = Conversation::where('workspace_id', $workspaceId)->where('status', 'open')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_conversations' => $totalConversations,
                'total_messages'      => $totalMessages,
                'answered_inquiries'  => $botMessages,
                'bot_resolved'        => $botResolved,
                'new_inquiries'       => $newInquiries,
                'resolution_rate'     => $resolutionRate . '%',
            ]
        ]);
    }
}
