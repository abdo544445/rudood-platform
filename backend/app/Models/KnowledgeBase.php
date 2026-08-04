<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    protected $fillable = [
        'bot_id', 'file_name', 'file_path', 'document_text', 'status',
    ];

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }
}
