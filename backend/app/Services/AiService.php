<?php

namespace App\Services;

use App\Models\Bot;
use Illuminate\Support\Facades\Http;

/**
 * AiService — Multi-Provider AI Router
 *
 * Routes requests to the correct AI provider based on Bot configuration.
 * Supported: OpenAI, Google Gemini, Anthropic Claude, OpenAI-compatible (custom).
 */
class AiService
{
    public function __construct(private Bot $bot) {}

    /**
     * Generate an AI reply for the given user message.
     *
     * @param string $userMessage   The customer's incoming message
     * @param string $context       Knowledge base context to inject into prompt
     * @return string               The AI-generated response
     */
    public function generateReply(string $userMessage, string $context = ''): string
    {
        $provider = $this->bot->ai_provider ?: 'gemini';
        $apiKey = $this->bot->api_key;

        if (!$apiKey) {
            $apiKey = match ($provider) {
                'gemini'    => env('GEMINI_API_KEY'),
                'openai'    => env('OPENAI_API_KEY'),
                'anthropic' => env('ANTHROPIC_API_KEY'),
                default     => env('GEMINI_API_KEY') ?: env('OPENAI_API_KEY'),
            };
        }

        if (!$apiKey) {
            return $this->getFallbackReply();
        }

        try {
            return match ($provider) {
                'openai'            => $this->callOpenAI($userMessage, $context, $apiKey),
                'gemini'            => $this->callGemini($userMessage, $context, $apiKey),
                'anthropic'         => $this->callAnthropic($userMessage, $context, $apiKey),
                'openai_compatible' => $this->callOpenAiCompatible($userMessage, $context, $apiKey),
                default             => $this->callGemini($userMessage, $context, $apiKey),
            };
        } catch (\Exception $e) {
            \Log::error('AI Service Error: ' . $e->getMessage());
            return $this->getFallbackReply();
        }
    }

    // ─── OpenAI (GPT-4o, GPT-4o-mini, etc.) ─────────────────────────────────

    private function callOpenAI(string $userMessage, string $context, string $apiKey): string
    {
        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $this->bot->model_type,
                'temperature' => $this->bot->temperature,
                'max_tokens'  => $this->bot->max_tokens,
                'messages'    => $this->buildOpenAiMessages($userMessage, $context),
            ]);

        return $response->json('choices.0.message.content') ?? $this->getFallbackReply();
    }

    // ─── OpenAI-Compatible (any provider with /v1/chat/completions endpoint) ─

    private function callOpenAiCompatible(string $userMessage, string $context, string $apiKey): string
    {
        $baseUrl = rtrim($this->bot->api_base_url ?? 'https://api.openai.com/v1', '/');

        $response = Http::withToken($apiKey)
            ->baseUrl($baseUrl)
            ->timeout(30)
            ->post('/chat/completions', [
                'model'       => $this->bot->model_type,
                'temperature' => $this->bot->temperature,
                'max_tokens'  => $this->bot->max_tokens,
                'messages'    => $this->buildOpenAiMessages($userMessage, $context),
            ]);

        return $response->json('choices.0.message.content') ?? $this->getFallbackReply();
    }

    // ─── Google Gemini ────────────────────────────────────────────────────────

    private function callGemini(string $userMessage, string $context, string $apiKey): string
    {
        $model = $this->bot->model_type ?: 'gemini-1.5-flash';
        $url   = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $systemParts = [
            ['text' => $this->buildSystemPrompt($context)],
        ];

        $response = Http::timeout(30)->post($url, [
            'system_instruction' => ['parts' => $systemParts],
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $userMessage]]],
            ],
            'generationConfig' => [
                'temperature' => $this->bot->temperature,
                'maxOutputTokens' => $this->bot->max_tokens,
            ],
        ]);

        return $response->json('candidates.0.content.parts.0.text') ?? $this->getFallbackReply();
    }

    // ─── Anthropic Claude ─────────────────────────────────────────────────────

    private function callAnthropic(string $userMessage, string $context, string $apiKey): string
    {
        $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])
            ->timeout(30)
            ->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->bot->model_type ?: 'claude-3-haiku-20240307',
                'max_tokens' => $this->bot->max_tokens,
                'system'     => $this->buildSystemPrompt($context),
                'messages'   => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

        return $response->json('content.0.text') ?? $this->getFallbackReply();
    }

    // ─── Shared Helpers ───────────────────────────────────────────────────────

    /**
     * Build the system prompt with persona + knowledge base context.
     */
    private function buildSystemPrompt(string $context): string
    {
        $persona = $this->bot->system_prompt
            ?? 'أنت مساعد ذكاء اصطناعي مفيد ومهني يرد على العملاء بلطف ودقة باللغة العربية.';

        $tone = match ($this->bot->bot_tone) {
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
     * Build messages array in OpenAI format.
     */
    private function buildOpenAiMessages(string $userMessage, string $context): array
    {
        return [
            ['role' => 'system', 'content' => $this->buildSystemPrompt($context)],
            ['role' => 'user',   'content' => $userMessage],
        ];
    }

    /**
     * Fallback reply when API key is missing or call fails.
     */
    private function getFallbackReply(): string
    {
        return $this->bot->welcome_message
            ?? 'شكراً لتواصلك معنا. سيقوم فريقنا بالرد عليك في أقرب وقت ممكن.';
    }
}
