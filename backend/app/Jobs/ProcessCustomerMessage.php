<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\Bot;
use App\Models\Channel;
use App\Models\AiDecisionLog;
use App\Services\AiService;
use App\Services\RagService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * ProcessCustomerMessage — The AI Automation Brain
 *
 * This job runs asynchronously in the background after a customer sends a message.
 * It performs the following steps:
 *
 *  1. Load the conversation, workspace bot, and message context
 *  2. Check if the bot is active for this workspace
 *  3. Scan auto_rules via RagService for a keyword match → if found, reply immediately
 *  4. If no rule matched, fetch prior conversation history + compile knowledge_bases context via RagService + call AI
 *  5. Save the AI reply as a bot Message in the DB
 *  6. Record an AiDecisionLog for full auditability & observability
 *  7. Publish the new message to Redis → Node.js WebSocket → Browser
 *  8. Dispatch outgoing reply to external channels (WhatsApp Cloud API / Telegram Bot)
 */
class ProcessCustomerMessage implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    // Retry up to 3 times with 30-second backoff if AI call fails
    public int $tries   = 3;
    public int $backoff = 30;

    public function __construct(
        private int $conversationId,
        private int $messageId
    ) {}

    public function handle(?RagService $ragService = null): void
    {
        $ragService = $ragService ?? app(RagService::class);
        $startTime = microtime(true);

        $conversation = Conversation::with('customer', 'workspace')->find($this->conversationId);
        if (!$conversation) return;

        // ── Human Takeover Check: if bot is paused, do not reply ───────────────
        if (!$conversation->isBotActive()) {
            return;
        }

        $workspace = $conversation->workspace;
        if ($workspace && !$workspace->hasRemainingQuota()) {
            \Log::warning("Workspace {$workspace->id} exceeded monthly message limit.");
            return;
        }

        $bot = Bot::where('workspace_id', $conversation->workspace_id)
                  ->where('is_active', true)
                  ->first();

        // If no active bot found for this workspace, do nothing
        if (!$bot) return;

        $customerMessage = Message::find($this->messageId);
        if (!$customerMessage) return;

        // ── Step 0: Sentiment Analysis & Urgency Escalation ───────────────────
        $aiService = new AiService($bot);
        try {
            $sentimentData = $aiService->analyzeSentimentAndUrgency($customerMessage->content);
            $conversation->update([
                'sentiment'         => $sentimentData['sentiment'],
                'is_escalated'      => $sentimentData['is_escalated'] || $conversation->is_escalated,
                'escalation_reason' => $sentimentData['reason'] ?? $conversation->escalation_reason,
            ]);
        } catch (\Throwable $e) {}

        $trigger = 'ai_api';
        $matchedKeywords = null;
        $context = null;

        // ── Step 1: Check Auto-Rules first (instant, no API call needed) ──────
        $ruleMatch = $ragService->checkAutoRules($conversation->workspace_id, $customerMessage->content);

        if ($ruleMatch !== null) {
            $trigger         = 'auto_rule';
            $replyText       = $ruleMatch['reply'];
            $matchedKeywords = is_array($ruleMatch['keywords'])
                ? implode(', ', $ruleMatch['keywords'])
                : (string) $ruleMatch['keywords'];
        } else {
            // ── Step 2: Multi-turn Conversational Memory & Knowledge Retrieval ──
            $history = Message::where('conversation_id', $conversation->id)
                ->where('id', '<', $customerMessage->id)
                ->orderByDesc('id')
                ->limit(6)
                ->get()
                ->reverse()
                ->values()
                ->toArray();

            $ragResult = $ragService->retrieveRelevantChunks($bot->id, $customerMessage->content);
            $context   = $ragResult['context'];

            $replyText = $aiService->generateReply($customerMessage->content, $context, $history);

            if ($replyText === $aiService->getFallbackReply()) {
                $trigger = 'fallback';
            }
        }

        if (!$replyText) return;

        // ── Step 3: Save bot reply to database & record usage ────────────────
        $botMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'bot',
            'content'         => $replyText,
        ]);

        $conversation->touch(); // Float conversation to top of sidebar list

        if ($workspace) {
            $workspace->recordUsage(1, mb_strlen($replyText));
        }

        // ── Step 4: Record AI Decision Audit Log ─────────────────────────────
        $durationMs = (int) max(1, round((microtime(true) - $startTime) * 1000));
        try {
            AiDecisionLog::create([
                'conversation_id'  => $conversation->id,
                'message_id'       => $botMessage->id,
                'trigger'          => $trigger,
                'matched_keywords' => $matchedKeywords,
                'context_sent'     => $context ? Str::limit($context, 2000) : null,
                'ai_provider'      => $bot->ai_provider ?: 'gemini',
                'model_type'       => $bot->model_type ?: 'default',
                'customer_message' => $customerMessage->content,
                'bot_reply'        => $replyText,
                'response_time_ms' => $durationMs,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('AI Decision Log creation failed: ' . $e->getMessage());
        }

        // ── Step 5: Publish to Redis → Node.js → Live Chat UI ────────────────
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id' => $conversation->id,
                    'workspace_id'    => $conversation->workspace_id,
                    'sender_type'     => 'bot',
                    'content'         => $botMessage->content,
                    'time'            => $botMessage->created_at->format('H:i'),
                    'message_id'      => $botMessage->id,
                ]));
            }
        } catch (\Throwable $e) {
            \Log::warning('Redis publish omitted: ' . $e->getMessage());
        }

        // ── Step 6: Dispatch Outgoing Channel Message (WhatsApp / Telegram) ──
        $this->dispatchOutgoingChannelMessage($conversation, $replyText);
    }

    /**
     * Send outgoing reply message via connected platform APIs.
     */
    private function dispatchOutgoingChannelMessage(Conversation $conversation, string $replyText): void
    {
        $customer = $conversation->customer;
        if (!$customer) return;

        $workspaceId = $conversation->workspace_id;

        // 1. WhatsApp Outgoing Dispatch
        if (($customer->platform === 'whatsapp' || !empty($customer->phone)) && !empty($customer->phone)) {
            $waChannel = Channel::where('workspace_id', $workspaceId)
                ->where('platform', 'whatsapp')
                ->first();

            if ($waChannel && $waChannel->isActive() && $waChannel->access_token && $waChannel->phone_number_id) {
                try {
                    Http::withToken($waChannel->access_token)
                        ->timeout(15)
                        ->post("https://graph.facebook.com/v19.0/{$waChannel->phone_number_id}/messages", [
                            'messaging_product' => 'whatsapp',
                            'to'                => ltrim($customer->phone, '+'),
                            'type'              => 'text',
                            'text'              => ['body' => $replyText],
                        ]);
                } catch (\Throwable $e) {
                    \Log::warning('WhatsApp outgoing reply error: ' . $e->getMessage());
                }
            }
        }

        // 2. Telegram Outgoing Dispatch
        if (($customer->platform === 'telegram' || !empty($customer->chat_id)) && !empty($customer->chat_id)) {
            $tgChannel = Channel::where('workspace_id', $workspaceId)
                ->where('platform', 'telegram')
                ->first();

            if ($tgChannel && $tgChannel->isActive() && $tgChannel->bot_token) {
                try {
                    Http::timeout(15)
                        ->post("https://api.telegram.org/bot{$tgChannel->bot_token}/sendMessage", [
                            'chat_id' => $customer->chat_id,
                            'text'    => $replyText,
                        ]);
                } catch (\Throwable $e) {
                    \Log::warning('Telegram outgoing reply error: ' . $e->getMessage());
                }
            }
        }

        // 3. Instagram Direct Outgoing Dispatch
        if ($customer->platform === 'instagram' && !empty($customer->chat_id)) {
            $igChannel = Channel::where('workspace_id', $workspaceId)
                ->where('platform', 'instagram')
                ->first();

            if ($igChannel && $igChannel->isActive() && ($igChannel->page_access_token || $igChannel->access_token)) {
                try {
                    $token = $igChannel->page_access_token ?: $igChannel->access_token;
                    Http::withToken($token)
                        ->timeout(15)
                        ->post("https://graph.facebook.com/v19.0/me/messages", [
                            'recipient' => ['id' => $customer->chat_id],
                            'message'   => ['text' => $replyText],
                        ]);
                } catch (\Throwable $e) {
                    \Log::warning('Instagram outgoing reply error: ' . $e->getMessage());
                }
            }
        }
    }
}
