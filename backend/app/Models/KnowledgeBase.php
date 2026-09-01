<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $fillable = [
        'bot_id', 'file_name', 'file_path', 'document_text', 'chunks_json', 'chunks_embeddings', 'status',
    ];

    protected $casts = [
        'chunks_json'       => 'array',
        'chunks_embeddings' => 'array',
    ];

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function chunks()
    {
        return $this->hasMany(KnowledgeChunk::class);
    }

    /**
     * Booted lifecycle: automatically index chunks and vector embeddings when saved.
     */
    protected static function booted(): void
    {
        static::saved(function (KnowledgeBase $kb) {
            if ($kb->wasRecentlyCreated || $kb->isDirty('document_text')) {
                $kb->syncChunksWithEmbeddings();
            }
        });
    }

    /**
     * Synchronize semantic chunks and generate database vector embeddings for pgvector search.
     */
    public function syncChunksWithEmbeddings(): array
    {
        $rawChunks = $this->chunks_list;
        $ragService = app(\App\Services\RagService::class);
        $workspaceId = $this->bot?->workspace_id ?? 1;

        // Delete existing chunks for this knowledge base
        KnowledgeChunk::where('knowledge_base_id', $this->id)->delete();

        $chunkRecords = [];
        $embeddingsMap = [];
        $normalizedStringChunks = [];

        foreach ($rawChunks as $idx => $rawChunk) {
            $chunkText = is_array($rawChunk) ? ($rawChunk['text'] ?? json_encode($rawChunk, JSON_UNESCAPED_UNICODE)) : (string) $rawChunk;
            $chunkText = trim($chunkText);
            if (empty($chunkText)) continue;

            $vector = $ragService->generateVectorEmbedding($chunkText);
            $tokenCount = count(preg_split('/\s+/u', $chunkText));

            $chunkModel = KnowledgeChunk::create([
                'knowledge_base_id' => $this->id,
                'bot_id'            => $this->bot_id,
                'workspace_id'      => $workspaceId,
                'chunk_index'       => $idx,
                'chunk_text'        => $chunkText,
                'embedding'         => $vector,
                'token_count'       => $tokenCount,
                'metadata'          => [
                    'file_name'   => $this->file_name,
                    'chunk_index' => $idx,
                ],
            ]);

            $chunkRecords[] = $chunkModel;
            $normalizedStringChunks[] = $chunkText;
            $embeddingsMap[] = [
                'index'     => $idx,
                'vector'    => $vector,
                'tokens'    => $tokenCount,
            ];
        }

        // Quietly update chunks_json and chunks_embeddings columns without triggering saved loop
        $this->newQuery()->where('id', $this->id)->update([
            'chunks_json'       => $normalizedStringChunks,
            'chunks_embeddings' => json_encode($embeddingsMap, JSON_UNESCAPED_UNICODE),
            'status'            => 'processed',
        ]);

        return $chunkRecords;
    }

    /**
     * Split document text into meaningful semantic chunks (paragraphs / segments).
     */
    public function getChunksAttribute(): array
    {
        return $this->chunks_list;
    }

    /**
     * Helper to compute semantic chunks list.
     */
    public function getChunksListAttribute(): array
    {
        if (!empty($this->chunks_json) && is_array($this->chunks_json)) {
            return array_values(array_map(function($c) {
                return is_array($c) ? ($c['text'] ?? json_encode($c, JSON_UNESCAPED_UNICODE)) : (string)$c;
            }, $this->chunks_json));
        }

        $text = trim($this->document_text ?? '');
        if (empty($text)) {
            return [];
        }

        // Split by multiple newlines (paragraphs)
        $paragraphs = preg_split('/\n\s*\n/', $text);
        $chunks = [];

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if (empty($para)) continue;

            // If a paragraph is very long (> 1200 chars), subdivide it by sentences or single newlines
            if (mb_strlen($para) > 1200) {
                $subSections = preg_split('/\n|(?<=[.!?؟])\s+/', $para);
                $buffer = '';
                foreach ($subSections as $sub) {
                    $sub = trim($sub);
                    if (empty($sub)) continue;
                    if (mb_strlen($buffer . "\n" . $sub) > 1000) {
                        if (!empty($buffer)) $chunks[] = $buffer;
                        $buffer = $sub;
                    } else {
                        $buffer = empty($buffer) ? $sub : $buffer . "\n" . $sub;
                    }
                }
                if (!empty($buffer)) $chunks[] = $buffer;
            } else {
                $chunks[] = $para;
            }
        }

        return array_values(array_filter($chunks, fn($c) => mb_strlen(trim($c)) > 5));
    }
}
