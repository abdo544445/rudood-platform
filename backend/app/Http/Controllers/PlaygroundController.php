<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\AutoRule;
use App\Models\KnowledgeBase;
use App\Models\AiDecisionLog;
use App\Services\AiService;
use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlaygroundController extends Controller
{
    /**
     * Get or create the bot for the current user's workspace.
     */
    private function getBot(): Bot
    {
        $workspaceId = auth()->user()->workspace_id;
        return Bot::firstOrCreate(
            ['workspace_id' => $workspaceId],
            [
                'name'          => 'مساعد المتجر الذكي',
                'system_prompt' => 'أنت مساعد ذكاء اصطناعي مفيد ومهني يرد على العملاء بلطف ودقة باللغة العربية.',
                'model_type'    => 'gemini-1.5-flash',
                'ai_provider'   => 'gemini',
                'bot_tone'      => 'friendly',
                'temperature'   => 0.7,
                'max_tokens'    => 1000,
                'is_active'     => true,
            ]
        );
    }

    /**
     * Display the full-screen AI Playground workbench.
     */
    public function index()
    {
        $bot = $this->getBot();
        $workspaceId = $bot->workspace_id;

        $rulesCount = AutoRule::where('workspace_id', $workspaceId)->count();
        $docsCount  = KnowledgeBase::where('bot_id', $bot->id)->count();
        $recentLogs = AiDecisionLog::orderByDesc('id')->take(8)->get();

        // Suggested test prompts for quick 1-click testing
        $presetPrompts = [
            'ما هي أوقات العمل والتوصيل لديكم؟',
            'هل المنتجات أصلية ومضمونة؟',
            'ما هي وسائل الدفع المتوفرة وما هي سياسة الاسترجاع؟',
            'أريد معرفة أسعار التراخيص والبرامج المتوفرة.',
        ];

        return view('playground', compact('bot', 'rulesCount', 'docsCount', 'recentLogs', 'presetPrompts'));
    }

    /**
     * Process an interactive playground test message (AJAX / JSON).
     */
    public function send(Request $request, RagService $ragService)
    {
        $request->validate([
            'message'       => 'required|string|max:2000',
            'history'       => 'nullable|array',
            'overrides'     => 'nullable|array',
            'enable_rag'    => 'nullable|boolean',
            'enable_rules'  => 'nullable|boolean',
        ]);

        $bot = $this->getBot();
        $message = trim($request->input('message'));
        $history = $request->input('history', []);
        $overrides = $request->input('overrides', []);
        $enableRag = $request->boolean('enable_rag', true);
        $enableRules = $request->boolean('enable_rules', true);

        $start = microtime(true);
        $trigger = 'ai_api';
        $matchedKeywords = null;
        $matchedChunks = [];
        $context = '';
        $ruleReply = null;

        $aiServiceInspector = new AiService($bot);
        $aiServiceInspector->setOverrides($overrides);

        // 1. Instant Auto-Rule matching (if enabled)
        if ($enableRules) {
            $ruleMatch = $ragService->checkAutoRules($bot->workspace_id, $message);
            if ($ruleMatch !== null) {
                $trigger = 'auto_rule';
                $ruleReply = $ruleMatch['reply'];
                $matchedKeywords = is_array($ruleMatch['keywords'])
                    ? implode(', ', array_slice($ruleMatch['keywords'], 0, 5))
                    : (string) $ruleMatch['keywords'];
            }
        }

        // 2. RAG Retrieval & LLM Generation
        if ($trigger === 'auto_rule') {
            $reply = $ruleReply;
        } else {
            // Retrieve relevant chunks from Knowledge Base
            if ($enableRag) {
                $ragData = $ragService->retrieveRelevantChunks($bot->id, $message);
                $matchedChunks = $ragData['chunks'] ?? [];
                $context = $ragData['context'] ?? '';
            }

            // Call AI Service with parameter overrides
            $reply = $aiServiceInspector->generateReply($message, $context, $history);

            if ($reply === $aiServiceInspector->getFallbackReply()) {
                $trigger = 'fallback';
            }
        }

        $latencyMs = round((microtime(true) - $start) * 1000);

        // Record telemetry log
        try {
            AiDecisionLog::create([
                'conversation_id'  => 1,
                'message_id'       => null,
                'trigger'          => $trigger,
                'matched_keywords' => $matchedKeywords,
                'context_sent'     => $context ? Str::limit($context, 2000) : null,
                'ai_provider'      => $overrides['ai_provider'] ?? $bot->ai_provider ?: 'gemini',
                'model_type'       => $overrides['model_type'] ?? $bot->model_type ?: 'default',
                'customer_message' => $message,
                'bot_reply'        => $reply,
                'response_time_ms' => $latencyMs,
            ]);
        } catch (\Throwable $e) {
            // non-fatal
        }

        // Build raw system prompt preview for inspector
        $fullSystemPrompt = $aiServiceInspector->buildSystemPrompt($context);

        return response()->json([
            'success'            => true,
            'reply'              => $reply,
            'trigger'            => $trigger,
            'matched_keywords'   => $matchedKeywords,
            'chunks'             => $matchedChunks,
            'context'            => $context,
            'latency_ms'         => $latencyMs,
            'provider'           => $overrides['ai_provider'] ?? $bot->ai_provider ?: 'gemini',
            'model'              => $overrides['model_type'] ?? $bot->model_type ?: 'gemini-1.5-flash',
            'system_prompt_used' => $fullSystemPrompt,
            'error_detail'       => $aiServiceInspector->getLastError(),
        ]);
    }

    /**
     * Persist current playground test settings as permanent bot defaults.
     */
    public function applyDefaults(Request $request)
    {
        $validated = $request->validate([
            'ai_provider'       => 'required|string|in:gemini,openai,anthropic,openai_compatible',
            'model_type'        => 'required|string|max:100',
            'temperature'       => 'required|numeric|min:0|max:1',
            'max_tokens'        => 'required|integer|min:50|max:4000',
            'bot_tone'          => 'required|string|in:friendly,formal,sales',
            'system_prompt'     => 'required|string|max:3000',
            'api_base_url'      => 'nullable|string|max:255',
            'enable_rag'        => 'nullable|boolean',
            'enable_auto_rules' => 'nullable|boolean',
        ]);

        $validated['enable_rag'] = $request->has('enable_rag');
        $validated['enable_auto_rules'] = $request->has('enable_auto_rules');

        $bot = $this->getBot();
        $bot->update($validated);

        return back()->with('status', 'تم حفظ وتطبيق إعدادات البوت بنجاح كإعدادات افتراضية ✓');
    }
}
