<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AiService;
use App\Services\RagService;
use App\Jobs\ProcessCustomerMessage;

class WidgetController extends Controller
{
    /**
     * Get widget configuration and branding for a specific workspace.
     */
    public function getConfig(int $workspace_id)
    {
        $workspace = Workspace::find($workspace_id);
        if (!$workspace) {
            return response()->json(['success' => false, 'message' => 'Workspace not found'], 404);
        }

        $bot = $workspace->bots()->first() ?? Bot::first();
        $channel = Channel::where('workspace_id', $workspace_id)
            ->where('platform', 'web')
            ->first();

        return response()->json([
            'success' => true,
            'config'  => [
                'bot_name'        => $bot?->name ?? 'مساعد المتجر الذكي',
                'primary_color'   => $channel?->widget_color ?? '#d4af37',
                'position'        => $channel?->widget_position ?? 'right',
                'welcome_message' => $channel?->widget_greeting ?: ($bot?->welcome_message ?? 'أهلاً بك! كيف أقدر أساعدك اليوم؟'),
                'is_active'       => $channel ? $channel->isActive() : true,
            ]
        ]);
    }

    /**
     * Handle incoming visitor message from Web Live Chat Widget.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'workspace_id'    => 'required|integer',
            'message'         => 'required|string|max:2000',
            'user_id'         => 'nullable|string|max:100',
            'conversation_id' => 'nullable|integer',
        ]);

        $workspaceId = (int) $request->workspace_id;
        $workspace   = Workspace::find($workspaceId);
        if (!$workspace) {
            return response()->json(['success' => false, 'message' => 'Workspace not found'], 404);
        }

        // Check Web Channel activation status
        $webChannel = Channel::where('workspace_id', $workspaceId)
            ->where('platform', 'web')
            ->first();

        if ($webChannel && !$webChannel->isActive()) {
            return response()->json([
                'success' => true,
                'reply'   => 'خدمة الدردشة المباشرة متوقفة مؤقتاً حالياً. يرجى التواصل معنا عبر وسائل التواصل الأخرى.',
            ]);
        }

        // Find or create customer
        $visitorUid = $request->user_id ?: ('web_visitor_' . uniqid());
        $customer = Customer::firstOrCreate(
            ['workspace_id' => $workspaceId, 'phone' => $visitorUid],
            [
                'name'     => 'زائر الموقع (' . substr($visitorUid, -5) . ')',
                'platform' => 'web',
            ]
        );

        // Find or create conversation
        $conversation = null;
        if ($request->conversation_id) {
            $conversation = Conversation::where('id', $request->conversation_id)
                ->where('workspace_id', $workspaceId)
                ->first();
        }

        if (!$conversation) {
            $conversation = Conversation::create([
                'workspace_id' => $workspaceId,
                'customer_id'  => $customer->id,
                'status'       => 'open',
            ]);
        }

        // 1. Record incoming customer message
        $custMsg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'customer',
            'content'         => $request->message,
        ]);

        $conversation->touch();

        // Broadcast to Live Chat Inbox via Redis
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id' => $conversation->id,
                    'workspace_id'    => $workspaceId,
                    'sender_type'     => 'customer',
                    'content'         => $custMsg->content,
                    'time'            => $custMsg->created_at->format('H:i'),
                    'message_id'      => $custMsg->id,
                    'customer_name'   => $customer->name,
                ]));
            }
        } catch (\Throwable $e) {}

        // 2. Check if bot is active (not paused by human agent takeover)
        if (!$conversation->isBotActive()) {
            return response()->json([
                'success'         => true,
                'conversation_id' => $conversation->id,
                'reply'           => 'تم تحويل محادثتك لأحد ممثلي خدمة العملاء وسيقوم بالرد عليك مباشرة هنا.',
            ]);
        }

        // 3. Generate AI Response
        $bot = $workspace->bots()->first() ?? Bot::first();
        $aiService  = new AiService($bot);
        $ragService = new RagService();

        // Analyze sentiment & urgency
        try {
            $sentiment = $aiService->analyzeSentimentAndUrgency($request->message);
            $conversation->update([
                'sentiment'         => $sentiment['sentiment'],
                'is_escalated'      => $sentiment['is_escalated'] || $conversation->is_escalated,
                'escalation_reason' => $sentiment['reason'] ?? $conversation->escalation_reason,
            ]);
        } catch (\Throwable $e) {}

        // Check Auto-rules first
        $ruleMatch = $ragService->checkAutoRules($workspaceId, $request->message);
        if ($ruleMatch !== null) {
            $replyText = $ruleMatch['reply'];
        } else {
            // Retrieve RAG chunks
            $ragResult = $ragService->retrieveRelevantChunks($bot->id, $request->message);
            $history = Message::where('conversation_id', $conversation->id)
                ->where('id', '<', $custMsg->id)
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->reverse()
                ->values()
                ->toArray();

            $replyText = $aiService->generateReply($request->message, $ragResult['context'], $history);
        }

        // Save bot reply
        $botMsg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'bot',
            'content'         => $replyText,
        ]);

        $workspace->recordUsage(1, mb_strlen($replyText));

        // Broadcast bot reply to Live Chat Inbox
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id' => $conversation->id,
                    'workspace_id'    => $workspaceId,
                    'sender_type'     => 'bot',
                    'content'         => $botMsg->content,
                    'time'            => $botMsg->created_at->format('H:i'),
                    'message_id'      => $botMsg->id,
                ]));
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success'         => true,
            'conversation_id' => $conversation->id,
            'reply'           => $replyText,
        ]);
    }

    /**
     * Load message history for active conversation.
     */
    public function getHistory(int $conversation_id)
    {
        $conversation = Conversation::find($conversation_id);
        if (!$conversation) {
            return response()->json(['success' => false, 'messages' => []]);
        }

        $messages = $conversation->messages()
            ->orderBy('id', 'asc')
            ->get(['id', 'sender_type', 'content', 'created_at']);

        return response()->json([
            'success'  => true,
            'messages' => $messages,
        ]);
    }
}
