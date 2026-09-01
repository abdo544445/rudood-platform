<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeChunk extends Model
{
    protected $fillable = [
        'knowledge_base_id',
        'bot_id',
        'workspace_id',
        'chunk_index',
        'chunk_text',
        'embedding',
        'token_count',
        'metadata',
    ];

    protected $casts = [
        'embedding' => 'array',
        'metadata'  => 'array',
    ];

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
