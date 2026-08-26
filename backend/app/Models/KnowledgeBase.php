<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $fillable = [
        'bot_id', 'file_name', 'file_path', 'document_text', 'chunks_json', 'status',
    ];

    protected $casts = [
        'chunks_json' => 'array',
    ];

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Split document text into meaningful semantic chunks (paragraphs / segments), using cached chunks_json if available.
     */
    public function getChunksAttribute(): array
    {
        if (!empty($this->chunks_json) && is_array($this->chunks_json)) {
            return $this->chunks_json;
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
