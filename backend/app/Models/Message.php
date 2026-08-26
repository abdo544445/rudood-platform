<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'content',
        'media_type',
        'media_url',
        'interactive_type',
        'interactive_data',
        'read_at',
    ];

    protected $casts = [
        'interactive_data' => 'array',
        'read_at'          => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
