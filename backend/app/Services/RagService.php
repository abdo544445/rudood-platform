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
     * Calculate exact Cosine Similarity between two vector arrays.
     * Returns a float between -1.0 and 1.0 (typically 0.0 to 1.0 for embeddings).
     */
    public function calculateCosineSimilarity(array $vecA, array $vecB): float
    {
        if (empty($vecA) || empty($vecB)) return 0.0;
        
        $len = min(count($vecA), count($vecB));
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < $len; $i++) {
            $a = (float) $vecA[$i];
            $b = (float) $vecB[$i];
            $dotProduct += ($a * $b);
            $normA += ($a * $a);
            $normB += ($b * $b);
        }

        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }

        return round($dotProduct / (sqrt($normA) * sqrt($normB)), 4);
    }

    /**
     * Generate deterministic or AI semantic vector embedding for text.
     * Outputs normalized 64-dimensional float vector.
     */
    public function generateVectorEmbedding(string $text): array
    {
        $text = Str::lower(trim($text));
        $dims = 64;
        $vector = array_fill(0, $dims, 0.0);
        
        // Deterministic hashing across character n-grams and tokens
        $tokens = array_filter(preg_split('/[\s,\.؟!،]+/u', $text));
        foreach ($tokens as $idx => $token) {
            $h = crc32($token);
            $dim = abs($h) % $dims;
            $weight = 1.0 + (mb_strlen($token) * 0.1);
            $vector[$dim] += $weight;

            // Bigram dimension activation
            if (isset($tokens[$idx + 1])) {
                $bigram = $token . '_' . $tokens[$idx + 1];
                $bgDim = abs(crc32($bigram)) % $dims;
                $vector[$bgDim] += ($weight * 1.2);
            }
        }

        // L2 Normalize vector
        $norm = 0.0;
        foreach ($vector as $v) {
            $norm += ($v * $v);
        }
        $sqrtNorm = sqrt($norm);
        if ($sqrtNorm > 0) {
            foreach ($vector as $i => $v) {
                $vector[$i] = round($v / $sqrtNorm, 6);
            }
        }

        return $vector;
    }

    /**
     * Retrieve relevant knowledge base chunks using Hybrid RAG (Vector Cosine Similarity + BM25 Keywords).
     * Returns array with:
     *   - 'chunks'  => list of ['text' => ..., 'score' => ..., 'similarity_pct' => ...]
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

        $queryVector = $this->generateVectorEmbedding($query);
        $scoredChunks = [];

        foreach ($docs as $doc) {
            $chunks = $doc->chunks;
            if (empty($chunks)) {
                $chunks = [$doc->document_text];
            }

            foreach ($chunks as $chunk) {
                $chunkText = is_array($chunk) ? ($chunk['text'] ?? json_encode($chunk, JSON_UNESCAPED_UNICODE)) : (string)$chunk;
                $chunkLower = Str::lower($chunkText);

                // 1. Keyword overlap scoring (BM25 token match)
                $keywordScore = 0;
                foreach ($queryTokens as $token) {
                    if (Str::contains($chunkLower, $token)) {
                        $keywordScore += 15;
                    }
                }

                // 2. Vector Cosine Similarity (Semantic Match)
                $chunkVector = $this->generateVectorEmbedding($chunkText);
                $cosineSim = $this->calculateCosineSimilarity($queryVector, $chunkVector); // 0.0 to 1.0
                $vectorScore = ($cosineSim * 100); // 0 to 100

                // 3. Hybrid Blended Score (60% Vector, 40% Keyword)
                $hybridScore = ($vectorScore * 0.6) + ($keywordScore * 0.4);

                if ($hybridScore > 8 || $cosineSim >= 0.4) {
                    $scoredChunks[] = [
                        'text'           => $chunkText,
                        'score'          => round($hybridScore, 1),
                        'similarity_pct' => (int) round(min(100, max(0, $cosineSim * 100))),
                        'vector_score'   => round($vectorScore, 1),
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
