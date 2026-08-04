<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;

class DashboardController extends Controller
{
    public function getStats()
    {
        $workspaceId = auth()->user()->workspace_id ?? 1;

        $totalConversations = Conversation::where('workspace_id', $workspaceId)->count();
        $totalMessages = Message::whereHas('conversation', fn($q) => $q->where('workspace_id', $workspaceId))->count();
        $botMessages = Message::where('sender_type', 'bot')
            ->whereHas('conversation', fn($q) => $q->where('workspace_id', $workspaceId))->count();

        $resolutionRate = $totalMessages > 0 ? round(($botMessages / $totalMessages) * 100) : 0;
        $newInquiries = Conversation::where('workspace_id', $workspaceId)->where('status', 'open')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_conversations' => $totalConversations,
                'total_messages'      => $totalMessages,
                'answered_inquiries'  => $botMessages,
                'new_inquiries'       => $newInquiries,
                'resolution_rate'     => $resolutionRate . '%',
            ]
        ]);
    }
}
