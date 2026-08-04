<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable as BusQueueable;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\AutoRule;
use App\Models\Bot;
use App\Models\KnowledgeBase;
use App\Services\AiService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * ProcessCustomerMessage — The AI Automation Brain
 *
 * This job runs asynchronously in the background after a customer sends a message.
 * It performs the following steps:
 *
 *  1. Load the conversation, workspace bot, and message context
 *  2. Check if the bot is active for this workspace
 *  3. Scan auto_rules for a keyword match → if found, reply immediately
 *  4. If no rule matched, compile knowledge_bases context + call the AI API
 *  5. Save the AI reply as a bot Message in the DB
 *  6. Publish the new message to Redis → Node.js WebSocket → Browser
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

    public function handle(): void
    {
        $conversation = Conversation::with('customer')->find($this->conversationId);
        if (!$conversation) return;

        $bot = Bot::where('workspace_id', $conversation->workspace_id)
                  ->where('is_active', true)
                  ->first();

        // If no active bot found for this workspace, do nothing
        if (!$bot) return;

        $customerMessage = Message::find($this->messageId);
        if (!$customerMessage) return;

        // ── Step 1: Check Auto-Rules first (instant, no API call needed) ──────
        $replyText = $this->checkAutoRules($conversation->workspace_id, $customerMessage->content);

        // ── Step 2: No rule matched → call AI with knowledge base context ─────
        if (!$replyText) {
            $context   = $this->buildKnowledgeContext($bot->id);
            $aiService = new AiService($bot);
            $replyText = $aiService->generateReply($customerMessage->content, $context);
        }

        if (!$replyText) return;

        // ── Step 3: Save bot reply to database ───────────────────────────────
        $botMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'bot',
            'content'         => $replyText,
        ]);

        $conversation->touch(); // Float conversation to top of sidebar list

        // ── Step 4: Publish to Redis → Node.js → Live Chat UI ────────────────
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
    }

    /**
     * Search auto_rules for a keyword match in the customer's message.
     * Returns the reply template if matched, or null.
     */
    private function checkAutoRules(int $workspaceId, string $message): ?string
    {
        $messageLower = Str::lower($message);

        $rules = AutoRule::where('workspace_id', $workspaceId)
                         ->where('is_active', true)
                         ->get();

        foreach ($rules as $rule) {
            $keywords = is_array($rule->keywords)
                ? $rule->keywords
                : (json_decode($rule->keywords ?? '', true) ?? []);

            if (!is_iterable($keywords)) continue;

            foreach ($keywords as $keyword) {
                if (!empty($keyword) && Str::contains($messageLower, Str::lower($keyword))) {
                    return $rule->reply_template;
                }
            }
        }

        return null;
    }

    /**
     * Compile all knowledge base documents for this bot into a single context string.
     * Truncated to avoid token overflow.
     */
    private function buildKnowledgeContext(int $botId): string
    {
        $docs = KnowledgeBase::where('bot_id', $botId)
                              ->whereNotNull('document_text')
                              ->get(['document_text']);

        if ($docs->isEmpty()) return '';

        $combined = $docs->map(fn($d) => $d->document_text)->implode("\n\n---\n\n");

        // Limit context to ~8000 characters to avoid token overflow
        return Str::limit($combined, 8000, '...');
    }
}
