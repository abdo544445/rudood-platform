<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Customer;
use App\Models\Channel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

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
        $filter = $request->get('filter', 'all');

        $query = Conversation::with(['customer', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->where('workspace_id', $workspace_id);

        if ($filter === 'unhandled' || $filter === 'open') {
            $query->where(function ($q) {
                $q->where('status', 'open')
                  ->orWhere('status', 'human_handling')
                  ->orWhere('is_escalated', true)
                  ->orWhere('is_bot_paused', true);
            });
        } elseif ($filter === 'escalated') {
            $query->where(function ($q) {
                $q->where('is_escalated', true)
                  ->orWhere('is_bot_paused', true)
                  ->orWhere('status', 'human_handling');
            });
        } elseif ($filter === 'resolved') {
            $query->where('status', 'resolved');
        }

        $conversations = $query->orderByDesc('updated_at')->get();

        // Calculate counts for filter pills
        $filterCounts = [
            'all'       => Conversation::where('workspace_id', $workspace_id)->count(),
            'unhandled' => Conversation::where('workspace_id', $workspace_id)
                ->where(function ($q) {
                    $q->where('status', 'open')
                      ->orWhere('status', 'human_handling')
                      ->orWhere('is_escalated', true)
                      ->orWhere('is_bot_paused', true);
                })->count(),
            'escalated' => Conversation::where('workspace_id', $workspace_id)
                ->where(function ($q) {
                    $q->where('is_escalated', true)
                      ->orWhere('is_bot_paused', true)
                      ->orWhere('status', 'human_handling');
                })->count(),
            'resolved'  => Conversation::where('workspace_id', $workspace_id)->where('status', 'resolved')->count(),
        ];

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

        // Canned Quick Replies for this workspace
        $cannedReplies = \App\Models\CannedReply::where('workspace_id', $workspace_id)
            ->orderBy('shortcut')
            ->get();

        // If no canned replies exist, create handy defaults
        if ($cannedReplies->isEmpty()) {
            $defaults = [
                ['shortcut' => '/iban', 'title' => 'الحساب البنكي', 'content' => 'رقم الآيبان الرسمي للمتجر: SA0000000000000000000000 (مصرف الراجحي - اسم الحساب: متجر ردود)'],
                ['shortcut' => '/shipping', 'title' => 'مدة الشحن', 'content' => 'يتم تجهيز وشحن الطلبات خلال 24-48 ساعة، والتوصيل يستغرق من 2 إلى 4 أيام عمل لكافة مناطق المملكة.'],
                ['shortcut' => '/return', 'title' => 'سياسة الاسترجاع', 'content' => 'يمكنك استبدال أو استرجاع المنتج خلال 14 يوماً من استلام الشحنة بشرط بقاء المنتج بحالته الأصلية.'],
                ['shortcut' => '/discount', 'title' => 'كود خصم ترحيبي', 'content' => 'يسعدنا تقديم كود خصم خاص لك (WELCOME10) بخصم 10% على إجمالي طلبك القادم!'],
            ];
            foreach ($defaults as $d) {
                \App\Models\CannedReply::create(array_merge($d, ['workspace_id' => $workspace_id]));
            }
            $cannedReplies = \App\Models\CannedReply::where('workspace_id', $workspace_id)->get();
        }

        return view('live-chat', compact('conversations', 'active', 'messages', 'cannedReplies', 'filter', 'filterCounts'));
    }

    /**
     * Toggle Human Takeover (Pause or Resume Bot for a specific conversation).
     */
    public function toggleBot(Request $request, int $id)
    {
        $conversation = Conversation::where('id', $id)
            ->where('workspace_id', $this->workspaceId())
            ->firstOrFail();

        $pause = $request->boolean('pause', !$conversation->is_bot_paused);
        $minutes = $request->input('minutes'); // optional timeout

        if ($pause) {
            $conversation->pauseBot($minutes ? (int)$minutes : null);
            \App\Models\AuditLog::record(
                'live_chat_takeover',
                "قام الموظف بإيقاف البوت مؤقتاً للمحادثة #{$conversation->id} لتدخل بشري",
                'chat',
                ['conversation_id' => $conversation->id, 'timeout_minutes' => $minutes]
            );
        } else {
            $conversation->resumeBot();
            \App\Models\AuditLog::record(
                'live_chat_resume',
                "تم استئناف ردود البوت التلقائية للمحادثة #{$conversation->id}",
                'chat',
                ['conversation_id' => $conversation->id]
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success'       => true,
                'is_bot_paused' => $conversation->is_bot_paused,
                'status'        => $conversation->status,
                'message'       => $pause ? 'تم إيقاف البوت وتفعيل التدخل البشري ✓' : 'تم استئناف ردود البوت التلقائية ✓',
            ]);
        }

        return back()->with('success', $pause ? 'تم إيقاف البوت وتفعيل التدخل البشري ✓' : 'تم استئناف ردود البوت التلقائية ✓');
    }

    /**
     * Update customer / conversation notes and tags.
     */
    public function updateNotes(Request $request, int $id)
    {
        $conversation = Conversation::where('id', $id)
            ->where('workspace_id', $this->workspaceId())
            ->firstOrFail();

        $tags = $request->input('tags');
        if (is_string($tags)) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $tags))));
        }

        $conversation->update([
            'notes'        => $request->input('notes', $conversation->notes),
            'tags'         => $tags ?? $conversation->tags,
            'is_escalated' => $request->has('is_escalated') ? $request->boolean('is_escalated') : $conversation->is_escalated,
            'sentiment'    => $request->input('sentiment', $conversation->sentiment),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم حفظ الملاحظات والوسوم بنجاح ✓']);
        }

        return back()->with('success', 'تم حفظ الملاحظات بنجاح ✓');
    }

    /**
     * Add a new canned quick reply.
     */
    public function storeCannedReply(Request $request)
    {
        $request->validate([
            'shortcut' => 'required|string|max:30',
            'title'    => 'required|string|max:100',
            'content'  => 'required|string|max:1000',
        ]);

        $shortcut = trim($request->shortcut);
        if (!str_starts_with($shortcut, '/')) {
            $shortcut = '/' . $shortcut;
        }

        $reply = \App\Models\CannedReply::updateOrCreate(
            ['workspace_id' => $this->workspaceId(), 'shortcut' => $shortcut],
            ['title' => $request->title, 'content' => $request->content]
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'reply' => $reply]);
        }

        return back()->with('success', 'تمت إضافة الرد السريع بنجاح ✓');
    }

    /**
     * Export all conversations of this workspace to CSV.
     */
    public function exportCsv()
    {
        $workspace_id = $this->workspaceId();
        $conversations = Conversation::with(['customer', 'messages'])
            ->where('workspace_id', $workspace_id)
            ->orderByDesc('created_at')
            ->get();

        $filename = 'rudood_conversations_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($conversations) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for proper Excel Arabic rendering
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['رقم المحادثة', 'اسم العميل', 'رقم الهاتف', 'القناة', 'الحالة', 'مشاعر العميل', 'حالة التصعيد', 'عدد الرسائل', 'تاريخ البدء', 'آخر تحديث']);

            foreach ($conversations as $c) {
                fputcsv($file, [
                    $c->id,
                    $c->customer->name ?? 'غير محدد',
                    $c->customer->phone ?? 'لا يوجد',
                    $c->customer->platform ?? 'whatsapp',
                    $c->status,
                    $c->sentiment ?? 'neutral',
                    $c->is_escalated ? 'نعم' : 'لا',
                    $c->messages->count(),
                    $c->created_at->format('Y-m-d H:i'),
                    $c->updated_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
                    'is_self'         => true,
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

    /**
     * Send an interactive WhatsApp message (Buttons, List Menu, or Catalog Cards) by Agent.
     */
    public function sendInteractive(Request $request, int $id)
    {
        $request->validate([
            'type'    => 'required|in:button,list,carousel',
            'content' => 'required|string',
        ]);

        $conversation = Conversation::with('customer')
            ->where('id', $id)
            ->where('workspace_id', $this->workspaceId())
            ->firstOrFail();

        $interactiveService = app(\App\Services\WhatsAppInteractiveService::class);
        $type = $request->type;
        $content = $request->content;
        $interactiveData = [];

        if ($type === 'button') {
            $buttons = $request->buttons ?? $interactiveService->getWelcomeButtons();
            $interactiveData = is_array($buttons) ? $buttons : json_decode($buttons, true);
        } elseif ($type === 'list') {
            $sections = $request->sections ?? $interactiveService->getStoreServicesListMenu();
            $interactiveData = is_array($sections) ? $sections : json_decode($sections, true);
        } elseif ($type === 'carousel') {
            $cards = $request->cards ?? $interactiveService->getFeaturedProductCards();
            $interactiveData = is_array($cards) ? $cards : json_decode($cards, true);
        }

        $message = Message::create([
            'conversation_id'  => $conversation->id,
            'sender_type'      => 'agent',
            'content'          => $content,
            'interactive_type' => $type,
            'interactive_data' => $interactiveData,
        ]);

        $conversation->touch();

        // Dispatch to WhatsApp if customer has phone
        $customer = $conversation->customer;
        if ($customer && !empty($customer->phone)) {
            $channel = Channel::where('workspace_id', $this->workspaceId())
                ->where('platform', 'whatsapp')
                ->first();

            if ($channel && $channel->isActive()) {
                $payload = null;
                if ($type === 'button') {
                    $payload = $interactiveService->buildButtonPayload($customer->phone, $content, $interactiveData);
                } elseif ($type === 'list') {
                    $payload = $interactiveService->buildListMenuPayload($customer->phone, $content, 'عرض الخيارات 📋', $interactiveData);
                }
                if ($payload) {
                    $interactiveService->sendInteractive($channel, $customer->phone, $payload);
                }
            }
        }

        // Publish to Redis live chat
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id'  => $conversation->id,
                    'workspace_id'     => $this->workspaceId(),
                    'sender_type'      => 'agent',
                    'content'          => $message->content,
                    'time'             => $message->created_at->format('H:i'),
                    'message_id'       => $message->id,
                    'interactive_type' => $message->interactive_type,
                    'interactive_data' => $message->interactive_data,
                    'is_self'          => true,
                ]));
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Upload an image or file attachment into the live conversation.
     */
    public function uploadAttachment(Request $request, int $id)
    {
        $request->validate([
            'attachment' => 'required|file|max:10240|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,txt,csv,xlsx',
            'caption'    => 'nullable|string|max:500',
        ]);

        $conversation = Conversation::where('id', $id)
            ->where('workspace_id', $this->workspaceId())
            ->firstOrFail();

        $file = $request->file('attachment');
        $origName = $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $fileSize = $file->getSize();

        // Categorize media type
        $mediaType = str_starts_with($mime, 'image/') ? 'image' : 'document';

        // Store file in public disk
        $workspaceId = $this->workspaceId();
        $folder = "chat_attachments/{$workspaceId}";
        $path = $file->store($folder, 'public');
        $mediaUrl = Storage::url($path);

        $caption = $request->caption ? trim($request->caption) : ($mediaType === 'image' ? "📷 صورة: {$origName}" : "📎 ملف: {$origName}");

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'agent',
            'content'         => $caption,
            'media_type'      => $mediaType,
            'media_url'       => $mediaUrl,
            'file_name'       => $origName,
            'file_size'       => $fileSize,
        ]);

        $conversation->touch();

        // Publish to Redis live chat
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id' => $conversation->id,
                    'workspace_id'    => $this->workspaceId(),
                    'sender_type'     => 'agent',
                    'content'         => $message->content,
                    'time'            => $message->created_at->format('H:i'),
                    'message_id'      => $message->id,
                    'media_type'      => $message->media_type,
                    'media_url'       => $message->media_url,
                    'file_name'       => $message->file_name,
                    'file_size'       => $message->file_size,
                    'is_self'         => true,
                ]));
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Mark a conversation as resolved and trigger automatic CSAT survey.
     */
    public function resolveConversation(Request $request, int $id)
    {
        $conversation = Conversation::with('customer')
            ->where('id', $id)
            ->where('workspace_id', $this->workspaceId())
            ->firstOrFail();

        $userName = auth()->user()->name ?? 'الموظف';
        $conversation->resolve($userName);

        // Prepare CSAT interactive survey message
        $csatContent = "شكراً لتواصلك معنا! يسعدنا دائماً خدمتك. نرجو منك تقييم تجربتك معنا اليوم لمساعدتنا على تحسين وتطوير الخدمة ⭐️";
        $csatButtons = [
            ['id' => 'csat_5', 'title' => '⭐️⭐️⭐️⭐️⭐️ ممتاز (5)'],
            ['id' => 'csat_4', 'title' => '⭐️⭐️⭐️⭐️ جيد جداً (4)'],
            ['id' => 'csat_3', 'title' => '⭐️⭐️⭐️ جيد (3)'],
        ];

        $surveyMsg = Message::create([
            'conversation_id'  => $conversation->id,
            'sender_type'      => 'bot',
            'content'          => $csatContent,
            'interactive_type' => 'button',
            'interactive_data' => $csatButtons,
        ]);

        // If customer is on WhatsApp, dispatch interactive buttons via WhatsApp Cloud API
        $customer = $conversation->customer;
        if ($customer && !empty($customer->phone)) {
            $channel = Channel::where('workspace_id', $this->workspaceId())
                ->where('platform', 'whatsapp')
                ->first();

            if ($channel && $channel->isActive()) {
                $waService = app(\App\Services\WhatsAppInteractiveService::class);
                $payload = $waService->buildButtonPayload($customer->phone, $csatContent, $csatButtons);
                $waService->sendInteractive($channel, $customer->phone, $payload);
            }
        }

        // Publish to Redis
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id'  => $conversation->id,
                    'workspace_id'     => $this->workspaceId(),
                    'sender_type'      => 'bot',
                    'content'          => $surveyMsg->content,
                    'time'             => $surveyMsg->created_at->format('H:i'),
                    'message_id'       => $surveyMsg->id,
                    'interactive_type' => $surveyMsg->interactive_type,
                    'interactive_data' => $surveyMsg->interactive_data,
                    'status_update'    => 'resolved',
                ]));
            }
        } catch (\Throwable $e) {}

        if ($request->expectsJson() || $request->wantsJson() || !$request->headers->has('referer')) {
            return response()->json([
                'success'      => true,
                'conversation' => $conversation,
                'survey'       => $surveyMsg,
                'message'      => 'تم إنهاء المحادثة وإرسال استبيان الرضا (CSAT) بنجاح ✓',
            ]);
        }

        return back()->with('success', 'تم إنهاء المحادثة وإرسال استبيان الرضا بنجاح ✓');
    }

    /**
     * Record CSAT rating submitted by customer.
     */
    public function submitCsat(Request $request, int $id)
    {
        $request->validate([
            'score'    => 'required|integer|between:1,5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $conversation = Conversation::where('id', $id)
            ->where('workspace_id', $this->workspaceId())
            ->firstOrFail();

        $conversation->recordCsat((int)$request->score, $request->feedback);

        // Create polite thank-you message
        $stars = str_repeat('⭐️', (int)$request->score);
        $thankYouMsg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'bot',
            'content'         => "شكراً جزيلاً لتقييمك الكريم ({$stars} - {$request->score}/5)! رأيك يهمنا دائماً ونسعد بخدمتك في أي وقت 🌸",
        ]);

        // Publish to Redis
        try {
            if (class_exists('Illuminate\Support\Facades\Redis')) {
                \Illuminate\Support\Facades\Redis::publish('rudood_chat_channel', json_encode([
                    'conversation_id' => $conversation->id,
                    'workspace_id'    => $this->workspaceId(),
                    'sender_type'     => 'bot',
                    'content'         => $thankYouMsg->content,
                    'time'            => $thankYouMsg->created_at->format('H:i'),
                    'message_id'      => $thankYouMsg->id,
                    'csat_score'      => $conversation->csat_score,
                ]));
            }
        } catch (\Throwable $e) {}

        return response()->json([
            'success'    => true,
            'csat_score' => $conversation->csat_score,
            'message'    => 'تم تسجيل تقييمك بنجاح، شكراً لك!',
        ]);
    }
}
