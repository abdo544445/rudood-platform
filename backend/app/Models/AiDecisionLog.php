<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiDecisionLog extends Model
{
    protected $fillable = [
        'conversation_id',
        'message_id',
        'trigger',
        'matched_keywords',
        'context_sent',
        'ai_provider',
        'model_type',
        'customer_message',
        'bot_reply',
        'response_time_ms',
    ];

    protected $casts = [
        'response_time_ms' => 'integer',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
