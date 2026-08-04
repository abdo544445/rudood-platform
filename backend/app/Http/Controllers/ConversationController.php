<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Customer;
use Illuminate\Support\Facades\Redis;

class ConversationController extends Controller
{
    private function workspaceId(): int
    {
        return auth()->user()->workspace_id;
    }

    /**
     * Show the Live Chat page.
     * Loads all conversations for this workspace, with the first one pre-selected.
     */
    public function index(Request $request)
    {
        $workspace_id = $this->workspaceId();

        // All conversations in sidebar (with last message preview)
        $conversations = Conversation::with(['customer', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->where('workspace_id', $workspace_id)
            ->orderByDesc('updated_at')
            ->get();

        // Active conversation from query param or first one
        $activeId = $request->get('conversation', $conversations->first()?->id);
        $active   = null;
        $messages = collect();

        if ($activeId) {
            $active = Conversation::with('customer')
                ->where('workspace_id', $workspace_id)
                ->find($activeId);

            if ($active) {
                $messages = Message::where('conversation_id', $active->id)
                    ->orderBy('created_at')
                    ->get();

                // Mark all as read
                Message::where('conversation_id', $active->id)
                    ->where('sender_type', 'customer')
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        return view('live-chat', compact('conversations', 'active', 'messages'));
    }

    /**
     * Send an agent message in a conversation (AJAX or form POST).
     */
    public function sendMessage(Request $request, int $id)
    {
        $request->validate(['content' => 'required|string']);

        $conversation = Conversation::where('id', $id)
            ->where('workspace_id', $this->workspaceId())
            ->firstOrFail();

        // Save message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'agent',
            'content'         => $request->content,
        ]);

        // Update conversation timestamp so it floats to top of list
        $conversation->touch();

        // Publish to Redis → Node.js → All connected browsers
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id' => $conversation->id,
                    'workspace_id'    => $this->workspaceId(),
                    'sender_type'     => 'agent',
                    'content'         => $message->content,
                    'time'            => $message->created_at->format('H:i'),
                    'message_id'      => $message->id,
                ]));
            }
        } catch (\Throwable $e) {
            \Log::warning('Redis publish omitted: ' . $e->getMessage());
        }

        // Return JSON for AJAX, or redirect for regular form submit
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->back();
    }

    /**
     * Show a specific conversation (used via AJAX fetch or direct URL).
     */
    public function show(int $id)
    {
        $conversation = Conversation::with('customer')
            ->where('workspace_id', $this->workspaceId())
            ->findOrFail($id);

        $messages = Message::where('conversation_id', $id)
            ->orderBy('created_at')
            ->get();

        if (request()->expectsJson()) {
            return response()->json([
                'conversation' => $conversation,
                'messages'     => $messages,
            ]);
        }

        return redirect()->route('live-chat.index', ['conversation' => $id]);
    }
}
