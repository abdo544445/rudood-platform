<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Channel;
use App\Jobs\ProcessCustomerMessage;
use Illuminate\Support\Facades\Redis;

/**
 * WebhookController
 *
 * Handles incoming messages from external platforms (WhatsApp Cloud API, Telegram, Instagram, Web Widget).
 * This is the main entry point that triggers the AI Automation Brain.
 */
class WebhookController extends Controller
{
    /**
     * Meta / WhatsApp Cloud API Webhook Verification (Handshake)
     * Route: GET /api/webhook/whatsapp
     */
    public function verifyWhatsApp(Request $request)
    {
        $mode      = $request->get('hub_mode', $request->get('hub.mode'));
        $token     = $request->get('hub_verify_token', $request->get('hub.verify_token'));
        $challenge = $request->get('hub_challenge', $request->get('hub.challenge'));

        $expectedToken = config('services.whatsapp.verify_token', env('WHATSAPP_VERIFY_TOKEN', 'rudood_secret'));

        // Check against default config token OR any workspace channel's verify_token
        $matched = ($token === $expectedToken);
        if (!$matched && $token) {
            $channels = Channel::where('platform', 'whatsapp')->whereNotNull('verify_token')->get();
            foreach ($channels as $channel) {
                if ($channel->verify_token === $token) {
                    $matched = true;
                    break;
                }
            }
        }

        if ($mode === 'subscribe' && $matched) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response()->json(['error' => 'Unauthorized token verification'], 403);
    }

    /**
     * Meta Instagram Direct & Comments Webhook Verification (Handshake)
     * Route: GET /api/webhook/instagram
     */
    public function verifyInstagram(Request $request)
    {
        $mode      = $request->get('hub_mode', $request->get('hub.mode'));
        $token     = $request->get('hub_verify_token', $request->get('hub.verify_token'));
        $challenge = $request->get('hub_challenge', $request->get('hub.challenge'));

        $expectedToken = config('services.instagram.verify_token', env('INSTAGRAM_VERIFY_TOKEN', 'rudood_instagram_secret'));

        $matched = ($token === $expectedToken);
        if (!$matched && $token) {
            $channels = Channel::where('platform', 'instagram')->whereNotNull('verify_token')->get();
            foreach ($channels as $channel) {
                if ($channel->verify_token === $token) {
                    $matched = true;
                    break;
                }
            }
        }

        if ($mode === 'subscribe' && $matched) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response()->json(['error' => 'Unauthorized Instagram verification'], 403);
    }

    /**
     * Meta Instagram Direct Messages & Comments Ingestion
     * Route: POST /api/webhook/instagram
     */
    public function handleInstagram(Request $request)
    {
        $body = $request->all();

        // 1. Handle Instagram Direct Messages
        if (isset($body['entry'][0]['messaging'][0])) {
            $msgEntry = $body['entry'][0]['messaging'][0];
            $senderId = $msgEntry['sender']['id'] ?? null;
            $text     = $msgEntry['message']['text'] ?? null;
            $igAccId  = $body['entry'][0]['id'] ?? null;

            if ($senderId && $text) {
                $channel = Channel::where('platform', 'instagram')
                    ->where(function ($q) use ($igAccId) {
                        if ($igAccId) $q->where('instagram_account_id', $igAccId);
                    })
                    ->first() ?? Channel::where('platform', 'instagram')->where('is_connected', true)->first();

                if ($channel && !$channel->isActive()) {
                    return response()->json(['status' => 'channel_paused']);
                }

                $workspaceId = $channel?->workspace_id ?? Workspace::where('status', 'active')->first()?->id ?? 1;

                $customer = Customer::firstOrCreate(
                    ['workspace_id' => $workspaceId, 'chat_id' => (string) $senderId],
                    ['name' => 'عميل إنستغرام (@' . substr($senderId, -4) . ')', 'platform' => 'instagram']
                );

                $conversation = Conversation::where('workspace_id', $workspaceId)
                    ->where('customer_id', $customer->id)
                    ->where('status', 'open')
                    ->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'workspace_id' => $workspaceId,
                        'customer_id'  => $customer->id,
                        'status'       => 'open',
                    ]);
                }

                $msg = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_type'     => 'customer',
                    'content'         => $text,
                ]);

                $conversation->touch();

                // Broadcast to Live Chat
                try {
                    if (class_exists('Illuminate\Support\Facades\Redis')) {
                        \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                            'conversation_id' => $conversation->id,
                            'workspace_id'    => $workspaceId,
                            'sender_type'     => 'customer',
                            'content'         => $msg->content,
                            'time'            => $msg->created_at->format('H:i'),
                            'message_id'      => $msg->id,
                            'customer_name'   => $customer->name,
                        ]));
                    }
                } catch (\Throwable $e) {}

                ProcessCustomerMessage::dispatch($conversation->id, $msg->id)->onQueue('ai-processing');

                return response()->json(['status' => 'ok', 'conversation_id' => $conversation->id]);
            }
        }

        // 2. Handle Instagram Post Comments
        if (isset($body['entry'][0]['changes'][0]['field']) && $body['entry'][0]['changes'][0]['field'] === 'comments') {
            $commentData = $body['entry'][0]['changes'][0]['value'] ?? [];
            $commentId   = $commentData['id'] ?? null;
            $text        = $commentData['text'] ?? null;
            $from        = $commentData['from'] ?? [];
            $fromId      = $from['id'] ?? null;
            $fromName    = $from['username'] ?? 'مستخدم إنستغرام';

            if ($commentId && $text) {
                $channel = Channel::where('platform', 'instagram')->where('is_connected', true)->first();
                if ($channel && $channel->isActive() && $channel->auto_reply_comments) {
                    $workspaceId = $channel->workspace_id;
                    $customer = Customer::firstOrCreate(
                        ['workspace_id' => $workspaceId, 'chat_id' => (string) $fromId],
                        ['name' => '@' . $fromName, 'platform' => 'instagram']
                    );

                    $conversation = Conversation::create([
                        'workspace_id' => $workspaceId,
                        'customer_id'  => $customer->id,
                        'status'       => 'open',
                    ]);

                    $msg = Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_type'     => 'customer',
                        'content'         => '[تعليق على منشور]: ' . $text,
                    ]);

                    ProcessCustomerMessage::dispatch($conversation->id, $msg->id)->onQueue('ai-processing');
                }
            }
        }

        return response()->json(['status' => 'acknowledged']);
    }

    /**
     * Telegram Bot Incoming Webhook Parser
     * Route: POST /api/webhook/telegram/{workspace_id?}
     */
    public function handleTelegram(Request $request, $workspace_id = null)
    {
        $update = $request->all();
        $message = $update['message'] ?? $update['edited_message'] ?? null;

        if (!$message) {
            return response()->json(['status' => 'ignored']);
        }

        $text = $message['text'] ?? '';
        $isVoice = false;
        $mediaType = 'text';
        $mediaUrl = null;

        if (isset($message['voice']) || isset($message['audio'])) {
            $isVoice = true;
            $mediaType = 'audio';
            $bot = Bot::where('workspace_id', $workspace_id)->first() ?? new Bot();
            $aiService = new \App\Services\AiService($bot);
            $transcription = $aiService->transcribeAudio('');
            $text = "🎙️ [رسالة صوتية]: " . $transcription;
        }

        if (!$text) {
            return response()->json(['status' => 'ignored']);
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        $from   = $message['from'] ?? [];
        $name   = trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? '')) ?: ($from['username'] ?? 'عميل تيليجرام');

        $workspaceId = $workspace_id;
        if (!$workspaceId) {
            $channel = Channel::where('platform', 'telegram')->where('is_connected', true)->first();
            $workspaceId = $channel?->workspace_id ?? Workspace::where('status', 'active')->first()?->id ?? 1;
        }

        $customer = Customer::firstOrCreate(
            [
                'workspace_id' => $workspaceId,
                'chat_id'      => $chatId,
            ],
            [
                'name'     => $name,
                'platform' => 'telegram',
            ]
        );

        $conversation = Conversation::where('workspace_id', $workspaceId)
            ->where('customer_id', $customer->id)
            ->where('status', 'open')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'workspace_id' => $workspaceId,
                'customer_id'  => $customer->id,
                'status'       => 'open',
            ]);
        }

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'customer',
            'content'         => $text,
            'media_type'      => $mediaType,
            'media_url'       => $mediaUrl,
        ]);

        $conversation->touch();

        // Broadcast to Live Chat UI
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id' => $conversation->id,
                    'workspace_id'    => $workspaceId,
                    'sender_type'     => 'customer',
                    'content'         => $msg->content,
                    'time'            => $msg->created_at->format('H:i'),
                    'message_id'      => $msg->id,
                    'customer_name'   => $customer->name,
                ]));
            }
        } catch (\Throwable $e) {}

        // Dispatch AI processing
        ProcessCustomerMessage::dispatch($conversation->id, $msg->id)
            ->onQueue('ai-processing');

        return response()->json(['status' => 'ok', 'conversation_id' => $conversation->id]);
    }

    /**
     * General Webhook for receiving customer messages (Direct/JSON)
     * Route: POST /api/webhook/incoming
     */
    public function incoming(Request $request)
    {
        $request->validate([
            'workspace_id'  => 'required|integer|exists:workspaces,id',
            'platform'      => 'nullable|string|in:whatsapp,telegram,instagram,web',
            'customer_name' => 'required|string|max:255',
            'message'       => 'required|string',
        ]);

        $workspaceId = $request->workspace_id;
        $workspace   = Workspace::find($workspaceId);

        // Validate webhook signature if workspace has a configured secret
        if ($workspace && !empty($workspace->webhook_secret)) {
            $signature = $request->header('X-Webhook-Signature');
            if (!$signature) {
                return response()->json(['error' => 'Missing X-Webhook-Signature header'], 401);
            }

            $rawContent = $request->getContent() ?: json_encode($request->all());
            $expected   = hash_hmac('sha256', $rawContent, $workspace->webhook_secret);

            if (!hash_equals($expected, $signature)) {
                return response()->json(['error' => 'Invalid webhook signature'], 401);
            }
        }

        $platform = $request->platform ?? 'web';

        // ── Find or create the customer ───────────────────────────────────────
        $phone = $request->customer_phone;
        $email = $request->customer_email;

        if ($phone) {
            $customer = Customer::firstOrCreate(
                [
                    'workspace_id' => $workspaceId,
                    'phone'        => $phone,
                ],
                [
                    'name'     => $request->customer_name,
                    'email'    => $email,
                    'platform' => $platform,
                ]
            );
        } else {
            $customer = Customer::create([
                'workspace_id' => $workspaceId,
                'name'         => $request->customer_name,
                'phone'        => null,
                'email'        => $email,
                'platform'     => $platform,
            ]);
        }

        // ── Find an open conversation, or start a new one ─────────────────────
        $conversation = Conversation::where('workspace_id', $workspaceId)
            ->where('customer_id', $customer->id)
            ->where('status', 'open')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'workspace_id' => $workspaceId,
                'customer_id'  => $customer->id,
                'status'       => 'open',
            ]);
        }

        // ── Save the customer message ─────────────────────────────────────────
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'customer',
            'content'         => $request->message,
        ]);

        $conversation->touch();

        // ── Broadcast the incoming message to the Live Chat UI via Redis ──────
        $payload = json_encode([
            'conversation_id' => $conversation->id,
            'workspace_id'    => $workspaceId,
            'sender_type'     => 'customer',
            'content'         => $message->content,
            'time'            => $message->created_at->format('H:i'),
            'message_id'      => $message->id,
            'customer_name'   => $customer->name,
        ]);

        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', $payload);
            }
        } catch (\Throwable $e) {
            \Log::warning('Webhook Redis publish failed: ' . $e->getMessage());
        }

        // ── Dispatch AI Processing to background queue ────────────────────────
        ProcessCustomerMessage::dispatch($conversation->id, $message->id)
            ->onQueue('ai-processing');

        return response()->json([
            'success'         => true,
            'conversation_id' => $conversation->id,
            'message_id'      => $message->id,
            'message'         => 'Message received. AI is processing...',
        ]);
    }

    /**
     * Test endpoint: simulate an incoming customer message without a real webhook.
     * Usage: GET /api/webhook/test?workspace_id=1&message=ما هي أوقات التوصيل
     */
    public function test(Request $request)
    {
        $workspaceId = $request->get('workspace_id', 1);
        $request->merge([
            'workspace_id'  => $workspaceId,
            'platform'      => 'web',
            'customer_name' => $request->get('customer_name', 'عميل تجريبي'),
            'customer_phone'=> $request->get('phone', '+966500000999'),
            'message'       => $request->get('message', 'مرحباً، هل يمكنكم مساعدتي؟'),
        ]);

        $workspace = Workspace::find($workspaceId);
        if ($workspace && !empty($workspace->webhook_secret)) {
            $rawContent = json_encode($request->all());
            $signature  = hash_hmac('sha256', $rawContent, $workspace->webhook_secret);
            $request->headers->set('X-Webhook-Signature', $signature);
        }

        return $this->incoming($request);
    }
}
