<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Bot;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Jobs\ProcessCustomerMessage;
use Illuminate\Support\Facades\Redis;

/**
 * WebhookController
 *
 * Handles incoming messages from external platforms (WhatsApp, Instagram, Web Widget).
 * This is the main entry point that triggers the AI Automation Brain.
 *
 * Routes:
 *   POST /api/webhook/incoming       → General webhook for all platforms
 *   POST /api/webhook/whatsapp/{id}  → WhatsApp-specific (workspace token in URL)
 *   GET  /api/webhook/test           → Simulate a customer message (for testing)
 */
class WebhookController extends Controller
{
    /**
     * Receive an incoming message.
     *
     * Expected JSON body:
     * {
     *   "workspace_token": "abc123",  // or workspace_id for internal use
     *   "platform":        "whatsapp",
     *   "customer_name":   "أحمد علي",
     *   "customer_phone":  "+966500000001",
     *   "message":         "ما هي أوقات التوصيل?"
     * }
     */
    public function incoming(Request $request)
    {
        $request->validate([
            'workspace_id'  => 'required|integer|exists:workspaces,id',
            'platform'      => 'nullable|string|in:whatsapp,instagram,web,telegram',
            'customer_name' => 'required|string|max:255',
            'message'       => 'required|string',
        ]);

        $workspaceId = $request->workspace_id;
        $platform    = $request->platform ?? 'web';

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
            Redis::publish('rudood_chat_channel', $payload);
        } catch (\Exception $e) {
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
        $request->merge([
            'workspace_id'  => $request->get('workspace_id', 1),
            'platform'      => 'web',
            'customer_name' => $request->get('customer_name', 'عميل تجريبي'),
            'customer_phone'=> $request->get('phone', '+966500000999'),
            'message'       => $request->get('message', 'مرحباً، هل يمكنكم مساعدتي؟'),
        ]);

        return $this->incoming($request);
    }
}
