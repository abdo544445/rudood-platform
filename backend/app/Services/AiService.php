<?php

namespace App\Services;

use App\Models\Bot;
use Illuminate\Support\Facades\Http;

/**
 * AiService — Multi-Provider AI Router
 *
 * Routes requests to the correct AI provider based on Bot configuration.
 * Supported: OpenAI, Google Gemini, Anthropic Claude, OpenAI-compatible (custom).
 * Supports Multi-Turn Conversational Memory (history window).
 */
class AiService
{
    private array $overrides = [];
    private ?string $lastError = null;

    public function __construct(private Bot $bot) {}

    /**
     * Get the latest error message if generation failed.
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Set temporary dynamic parameter overrides for playground/testing.
     */
    public function setOverrides(array $overrides): self
    {
        $this->overrides = $overrides;
        return $this;
    }

    /**
     * Generate an AI reply for the given user message.
     *
     * @param string $userMessage   The customer's incoming message
     * @param string $context       Knowledge base context to inject into prompt
     * @param array  $history       Prior conversation messages array: [['role' => 'user'|'assistant', 'content' => '...'], ...]
     * @param array  $overrides     Optional runtime overrides (model, temperature, prompt, provider)
     * @return string               The AI-generated response
     */
    public function generateReply(string $userMessage, string $context = '', array $history = [], array $overrides = []): string
    {
        if (!empty($overrides)) {
            $this->overrides = array_merge($this->overrides, $overrides);
        }

        $this->lastError = null;
        $provider = $this->overrides['ai_provider'] ?? $this->bot->ai_provider ?: 'gemini';
        $apiKey = $this->overrides['api_key'] ?? $this->bot->api_key;

        if (!$apiKey) {
            $apiKey = match ($provider) {
                'gemini'            => env('GEMINI_API_KEY'),
                'openai'            => env('OPENAI_API_KEY'),
                'anthropic'         => env('ANTHROPIC_API_KEY'),
                'openai_compatible' => $this->bot->api_key ?: env('OPENAI_API_KEY'),
                default             => env('GEMINI_API_KEY') ?: env('OPENAI_API_KEY'),
            };
        }

        if (!$apiKey) {
            $this->lastError = "مفتاح API الخاص بمزود الذكاء الاصطناعي ({$provider}) غير متوفر. يرجى إضافته في ملف .env أو إعدادات البوت.";
            return $this->getFallbackReply();
        }

        $normalizedHistory = $this->normalizeHistory($history);

        try {
            return match ($provider) {
                'openai'            => $this->callOpenAI($userMessage, $context, $apiKey, $normalizedHistory),
                'gemini'            => $this->callGemini($userMessage, $context, $apiKey, $normalizedHistory),
                'anthropic'         => $this->callAnthropic($userMessage, $context, $apiKey, $normalizedHistory),
                'openai_compatible' => $this->callOpenAiCompatible($userMessage, $context, $apiKey, $normalizedHistory),
                default             => $this->callGemini($userMessage, $context, $apiKey, $normalizedHistory),
            };
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            \Log::error('AI Service Error: ' . $e->getMessage());
            return $this->getFallbackReply();
        }
    }

    // ─── OpenAI (GPT-4o, GPT-4o-mini, etc.) ─────────────────────────────────

    private function callOpenAI(string $userMessage, string $context, string $apiKey, array $history = []): string
    {
        $model = $this->overrides['model_type'] ?? $this->bot->model_type ?: 'gpt-4o-mini';
        $temp  = $this->overrides['temperature'] ?? $this->bot->temperature ?? 0.7;
        $maxT  = $this->overrides['max_tokens'] ?? $this->bot->max_tokens ?? 1000;

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $model,
                'temperature' => (float) $temp,
                'max_tokens'  => (int) $maxT,
                'messages'    => $this->buildOpenAiMessages($userMessage, $context, $history),
            ]);

        if ($response->successful() && $content = $response->json('choices.0.message.content')) {
            return $content;
        }

        $this->lastError = 'OpenAI Error (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body());
        \Log::error($this->lastError);
        return $this->getFallbackReply();
    }

    // ─── OpenAI-Compatible (any provider with /v1/chat/completions endpoint) ─

    private function callOpenAiCompatible(string $userMessage, string $context, string $apiKey, array $history = []): string
    {
        $baseUrl = rtrim($this->overrides['api_base_url'] ?? $this->bot->api_base_url ?? 'https://api.openai.com/v1', '/');
        $model   = $this->overrides['model_type'] ?? $this->bot->model_type ?: 'gpt-4o-mini';
        $temp    = $this->overrides['temperature'] ?? $this->bot->temperature ?? 0.7;
        $maxT    = $this->overrides['max_tokens'] ?? $this->bot->max_tokens ?? 1000;

        $url   = rtrim($baseUrl, '/') . '/chat/completions';
        $response = Http::withToken($apiKey)
            ->timeout(25)
            ->post($url, [
                'model'       => $model,
                'temperature' => (float) $temp,
                'max_tokens'  => (int) $maxT,
                'messages'    => $this->buildOpenAiMessages($userMessage, $context, $history),
            ]);

        if ($response->successful() && $content = $response->json('choices.0.message.content')) {
            return $content;
        }

        $this->lastError = 'OpenAI-Compatible Error (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body());
        \Log::error($this->lastError);
        return $this->getFallbackReply();
    }

    // ─── Google Gemini ────────────────────────────────────────────────────────

    private function callGemini(string $userMessage, string $context, string $apiKey, array $history = []): string
    {
        $model = $this->overrides['model_type'] ?? $this->bot->model_type ?: 'gemini-1.5-flash';
        $temp  = $this->overrides['temperature'] ?? $this->bot->temperature ?? 0.7;
        $maxT  = $this->overrides['max_tokens'] ?? $this->bot->max_tokens ?? 1000;
        $url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $systemParts = [
            ['text' => $this->buildSystemPrompt($context)],
        ];

        $contents = [];
        foreach ($history as $item) {
            $contents[] = [
                'role'  => $item['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $item['content']]],
            ];
        }
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $response = Http::timeout(30)->post($url, [
            'system_instruction' => ['parts' => $systemParts],
            'contents'           => $contents,
            'generationConfig'   => [
                'temperature'     => (float) $temp,
                'maxOutputTokens' => (int) $maxT,
            ],
        ]);

        if ($response->successful()) {
            $candidate = $response->json('candidates.0.content.parts.0.text');
            if ($candidate) {
                return $candidate;
            }
        }

        $this->lastError = 'Gemini API Error (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body());
        \Log::error($this->lastError);
        return $this->getFallbackReply();
    }

    // ─── Anthropic Claude ─────────────────────────────────────────────────────

    private function callAnthropic(string $userMessage, string $context, string $apiKey, array $history = []): string
    {
        $model = $this->overrides['model_type'] ?? $this->bot->model_type ?: 'claude-3-haiku-20240307';
        $maxT  = $this->overrides['max_tokens'] ?? $this->bot->max_tokens ?? 1000;

        $messages = [];
        foreach ($history as $item) {
            $messages[] = [
                'role'    => $item['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $item['content'],
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => $model,
                'max_tokens' => (int) $maxT,
                'system'     => $this->buildSystemPrompt($context),
                'messages'   => $messages,
            ]);

        if ($response->successful() && $content = $response->json('content.0.text')) {
            return $content;
        }

        $this->lastError = 'Claude API Error (' . $response->status() . '): ' . ($response->json('error.message') ?? $response->body());
        \Log::error($this->lastError);
        return $this->getFallbackReply();
    }

    // ─── Shared Helpers ───────────────────────────────────────────────────────

    /**
     * Build the system prompt with persona + knowledge base context.
     */
    public function buildSystemPrompt(string $context): string
    {
        $persona = $this->overrides['system_prompt']
            ?? $this->bot->system_prompt
            ?? 'أنت مساعد ذكاء اصطناعي مفيد ومهني يرد على العملاء بلطف ودقة باللغة العربية.';

        $toneVal = $this->overrides['bot_tone'] ?? $this->bot->bot_tone;
        $tone = match ($toneVal) {
            'formal'   => 'يجب أن تكون ردودك احترافية ورسمية.',
            'sales'    => 'يجب أن تكون ردودك تسويقية، تشجع العميل على الشراء.',
            default    => 'يجب أن تكون ردودك ودودة وترحيبية.',
        };

        $prompt = "{$persona}\n\n{$tone}\n\nأجب دائماً باللغة العربية ما لم يكتب العميل بلغة أخرى.";

        if ($context) {
            $prompt .= "\n\n--- معلومات عن المتجر / الشركة (استخدمها للرد) ---\n{$context}";
        }

        return $prompt;
    }

    /**
     * Build messages array in OpenAI format with multi-turn history.
     */
    public function buildOpenAiMessages(string $userMessage, string $context, array $history = []): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->buildSystemPrompt($context)],
        ];

        foreach ($history as $item) {
            $messages[] = [
                'role'    => $item['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $item['content'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $messages;
    }

    /**
     * Normalize various history shapes (Eloquent collections, arrays, sender_types) into standard ['role', 'content'].
     */
    private function normalizeHistory(array $history): array
    {
        $normalized = [];

        foreach ($history as $item) {
            if (is_object($item)) {
                $role = in_array($item->sender_type ?? '', ['bot', 'agent', 'assistant']) ? 'assistant' : 'user';
                $content = $item->content ?? '';
            } elseif (is_array($item)) {
                if (isset($item['role'])) {
                    $role = in_array($item['role'], ['assistant', 'model', 'bot', 'agent']) ? 'assistant' : 'user';
                } else {
                    $sender = $item['sender_type'] ?? 'customer';
                    $role = in_array($sender, ['bot', 'agent', 'assistant']) ? 'assistant' : 'user';
                }
                $content = $item['content'] ?? '';
            } else {
                continue;
            }

            if (!empty(trim($content))) {
                $normalized[] = [
                    'role'    => $role,
                    'content' => trim($content),
                ];
            }
        }

        return $normalized;
    }

    /**
     * Automatically extract FAQ question & answer pairs from raw document text using AI.
     */
    public function extractFaqFromDocument(string $documentText, int $limit = 5): array
    {
        $truncatedText = \Illuminate\Support\Str::limit(trim($documentText), 4500);
        if (empty($truncatedText)) {
            return [];
        }

        $systemPrompt = "أنت خبير في تحليل المستندات واستخراج الأسئلة الشائعة (FAQ) بدقة عالية. 
مهمتك استخراج عدد {$limit} أسئلة شائعة متوقعة مع إجاباتها الدقيقة من محتوى المستند المقدم.
لكل سؤال، استخرج أيضاً قائمة من 3 إلى 5 كلمات مفتاحية فريدة (keywords) باللغة العربية.
يجب أن يكون الرد بصيغة JSON فقط مصفوفة كائنات، بدون أي شرح إضافي أو نصوص خارج الـ JSON:
[
  {
    \"question\": \"السؤال المستخرج هنا؟\",
    \"answer\": \"الإجابة الشافية المستخلصة من النص.\",
    \"keywords\": [\"كلمة1\", \"كلمة2\", \"كلمة3\"]
  }
]";

        $userPrompt = "النص المستخرج من المستند:\n\n" . $truncatedText;

        try {
            $rawResponse = $this->generateReply($userPrompt, '', [], [
                'system_prompt' => $systemPrompt,
                'temperature'   => 0.3,
            ]);

            // Strip markdown backticks if returned ```json ... ```
            $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawResponse));
            $parsed = json_decode($cleanJson, true);
            if (!is_array($parsed) && preg_match('/\[\s*\{.*\}\s*\]/s', $rawResponse, $matches)) {
                $parsed = json_decode($matches[0], true);
            }

            if (is_array($parsed) && !empty($parsed)) {
                return array_slice($parsed, 0, $limit);
            }
        } catch (\Throwable $e) {
            \Log::warning('AI FAQ Extraction warning: ' . $e->getMessage());
        }

        // Heuristic fallback: extract sentences and generate Q&As from text structure
        $sentences = preg_split('/(?<=[.!?؟\n])\s+/u', $truncatedText);
        $fallbackFaqs = [];
        $i = 1;
        foreach ($sentences as $s) {
            $s = trim($s);
            if (mb_strlen($s) > 25 && count($fallbackFaqs) < $limit) {
                $words = array_filter(explode(' ', $s), fn($w) => mb_strlen($w) > 2);
                $fallbackFaqs[] = [
                    'question' => "سؤال {$i}: ما هو تفصيل: " . mb_substr($s, 0, 45) . "؟",
                    'answer'   => $s,
                    'keywords' => array_values(array_slice($words, 0, 4)),
                ];
                $i++;
            }
        }

        return $fallbackFaqs;
    }

    /**
     * Query provider endpoint to dynamically fetch the list of available models.
     */
    public function fetchAvailableModels(string $provider, ?string $apiKey = null, ?string $baseUrl = null): array
    {
        $apiKey = $apiKey ?: $this->bot->api_key;
        if (!$apiKey) {
            $apiKey = match ($provider) {
                'gemini'            => env('GEMINI_API_KEY'),
                'openai'            => env('OPENAI_API_KEY'),
                'anthropic'         => env('ANTHROPIC_API_KEY'),
                'openai_compatible' => $this->bot->api_key ?: env('OPENAI_API_KEY'),
                default             => env('GEMINI_API_KEY'),
            };
        }

        if (!$apiKey && $provider !== 'openai_compatible') {
            return [
                'success' => false,
                'message' => 'يرجى إدخال مفتاح الـ API أولاً لجلب النماذج.',
                'models'  => [],
            ];
        }

        try {
            if ($provider === 'openai' || $provider === 'openai_compatible') {
                $base = rtrim($baseUrl ?: $this->bot->api_base_url ?: 'https://api.openai.com/v1', '/');
                $url  = $base . '/models';

                $res = Http::withToken($apiKey)->timeout(12)->get($url);
                if ($res->successful()) {
                    $list = collect($res->json('data', []))->pluck('id')->filter()->values()->toArray();
                    if (!empty($list)) {
                        return ['success' => true, 'models' => $list];
                    }
                }
            } elseif ($provider === 'gemini') {
                $url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";
                $res = Http::timeout(12)->get($url);
                if ($res->successful()) {
                    $list = collect($res->json('models', []))
                        ->pluck('name')
                        ->map(fn($m) => str_replace('models/', '', $m))
                        ->filter(fn($m) => str_contains($m, 'gemini') || str_contains($m, 'flash') || str_contains($m, 'pro'))
                        ->values()
                        ->toArray();
                    if (!empty($list)) {
                        return ['success' => true, 'models' => $list];
                    }
                }
            } elseif ($provider === 'anthropic') {
                return [
                    'success' => true,
                    'models'  => [
                        'claude-3-5-sonnet-20240620',
                        'claude-3-opus-20240229',
                        'claude-3-sonnet-20240229',
                        'claude-3-haiku-20240307',
                    ],
                ];
            }
        } catch (\Throwable $e) {
            \Log::warning('fetchAvailableModels live query failed: ' . $e->getMessage());
        }

        $defaults = match ($provider) {
            'gemini'            => ['gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.0-flash'],
            'openai'            => ['gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo', 'gpt-3.5-turbo'],
            'anthropic'         => ['claude-3-5-sonnet-20240620', 'claude-3-haiku-20240307'],
            'openai_compatible' => ['gpt-4o-mini', 'llama-3.1-70b', 'mistral-large'],
            default             => ['gemini-1.5-flash', 'gemini-1.5-pro'],
        };

        return [
            'success' => true,
            'models'  => $defaults,
            'notice'  => 'تم عرض النماذج الشائعة للمزود.',
        ];
    }

    /**
     * Fallback reply when API key is missing or call fails.
     */
    public function getFallbackReply(): string
    {
        return $this->bot->welcome_message
            ?? 'شكراً لتواصلك معنا. سيقوم فريقنا بالرد عليك في أقرب وقت ممكن.';
    }

    /**
     * Analyze customer message sentiment, frustration, and detect escalation triggers.
     */
    public function analyzeSentimentAndUrgency(string $message): array
    {
        $clean = mb_strtolower(trim($message));
        if (empty($clean)) {
            return ['sentiment' => 'neutral', 'is_escalated' => false, 'reason' => null];
        }

        // Escalation trigger terms (anger, legal threats, ministry complaints, severe frustration)
        $urgentKeywords = [
            'وزارة التجارة'  => 'تهديد بالشكوى لوزارة التجارة',
            'بلاغ تجاري'     => 'تهديد بتقديم بلاغ تجاري',
            'احتيال'         => 'اتهام بالاحتيال أو النصب',
            'نصاب'           => 'اتهام بالنصب',
            'سرقة'           => 'ادعاء سرقة أموال أو بضاعة',
            'محامي'          => 'ذكر إجراءات قانونية',
            'شرطة'           => 'ذكر الشرطة أو الجهات الأمنية',
            'حولني لمدير'    => 'طلب التحدث مع الإدارة العليا أو المشرف',
            'كلم المشرف'     => 'طلب مشرف فوري',
            'استرداد فوري'   => 'مطالبة عاجلة باسترداد أموال',
            'تاخير غير مقبول'=> 'تأخير شديد وغير مقبول',
            'سيء جدا'        => 'تقييم غاضب جداً',
        ];

        foreach ($urgentKeywords as $word => $reason) {
            if (str_contains($clean, $word)) {
                return [
                    'sentiment'    => 'urgent',
                    'is_escalated' => true,
                    'reason'       => $reason,
                ];
            }
        }

        // Negative sentiment words
        $negativeWords = ['زعلان', 'غاضب', 'تاخرتوا', 'سيء', 'ما وصل', 'خربان', 'تالف', 'ردوا علي', 'وينكم', 'ليش التأخير'];
        foreach ($negativeWords as $neg) {
            if (str_contains($clean, $neg)) {
                return [
                    'sentiment'    => 'negative',
                    'is_escalated' => false,
                    'reason'       => 'استياء عام من العميل',
                ];
            }
        }

        // Positive sentiment words
        $positiveWords = ['شكرا', 'ممتاز', 'رائع', 'جزاكم الله خير', 'تسلم', 'مبدعين', 'افضل متجر', 'يعطيكم العافية'];
        foreach ($positiveWords as $pos) {
            if (str_contains($clean, $pos)) {
                return [
                    'sentiment'    => 'positive',
                    'is_escalated' => false,
                    'reason'       => null,
                ];
            }
        }

        return [
            'sentiment'    => 'neutral',
            'is_escalated' => false,
            'reason'       => null,
        ];
    }
}
