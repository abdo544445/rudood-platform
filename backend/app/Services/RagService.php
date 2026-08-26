<?php

namespace App\Services;

use App\Models\AutoRule;
use App\Models\KnowledgeBase;
use Illuminate\Support\Str;

class RagService
{
    /**
     * Search auto_rules for a keyword match in the customer's query.
     * Returns ['reply' => ..., 'keywords' => [...]] or null.
     */
    public function checkAutoRules(int $workspaceId, string $message): ?array
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
                    return [
                        'reply'    => $rule->reply_template,
                        'keywords' => is_array($keywords) ? array_values($keywords) : [$keyword],
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Retrieve relevant knowledge base chunks using semantic keyword & similarity matching (RAG).
     * Returns array with:
     *   - 'chunks'  => list of ['text' => ..., 'score' => ...]
     *   - 'context' => compiled string ready for LLM injection
     */
    public function retrieveRelevantChunks(int $botId, string $query): array
    {
        $docs = KnowledgeBase::where('bot_id', $botId)
                              ->whereNotNull('document_text')
                              ->get();

        if ($docs->isEmpty()) {
            return [
                'chunks'  => [],
                'context' => '',
            ];
        }

        $queryLower = Str::lower($query);
        $queryTokens = array_filter(
            preg_split('/[\s,\.؟!،]+/u', $queryLower),
            fn($token) => mb_strlen(trim($token)) > 1
        );

        $scoredChunks = [];

        foreach ($docs as $doc) {
            $chunks = $doc->chunks;
            if (empty($chunks)) {
                $chunks = [$doc->document_text];
            }

            foreach ($chunks as $chunk) {
                $chunkText = is_array($chunk) ? ($chunk['text'] ?? json_encode($chunk, JSON_UNESCAPED_UNICODE)) : (string)$chunk;
                $chunkLower = Str::lower($chunkText);
                $score = 0;

                // 1. Keyword overlap scoring (+10 per matched token)
                foreach ($queryTokens as $token) {
                    if (Str::contains($chunkLower, $token)) {
                        $score += 10;
                    }
                }

                // 2. String similarity scoring (+ 0.5 * similarity %)
                similar_text($queryLower, Str::limit($chunkLower, 300), $similarityPct);
                $score += ($similarityPct * 0.5);

                if ($score > 5) {
                    $scoredChunks[] = [
                        'text'  => $chunkText,
                        'score' => round($score, 1),
                    ];
                }
            }
        }

        if (!empty($scoredChunks)) {
            usort($scoredChunks, fn($a, $b) => $b['score'] <=> $a['score']);
            $topChunks = array_slice($scoredChunks, 0, 4);
            $context = implode("\n\n---\n\n", array_column($topChunks, 'text'));

            return [
                'chunks'  => $topChunks,
                'context' => Str::limit($context, 6000, '...'),
            ];
        }

        // Fallback: concatenate initial portions of all documents
        $combined = $docs->map(fn($d) => Str::limit($d->document_text, 1500))->implode("\n\n---\n\n");

        return [
            'chunks'  => [],
            'context' => Str::limit($combined, 6000, '...'),
        ];
    }
}
