<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    public const STATUS_OPEN           = 'open';
    public const STATUS_CLOSED_BY_BOT  = 'closed_by_bot';
    public const STATUS_HUMAN_HANDLING = 'human_handling';
    public const STATUS_CLOSED         = 'closed';

    public static array $validStatuses = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED_BY_BOT,
        self::STATUS_HUMAN_HANDLING,
        self::STATUS_CLOSED,
    ];

    protected $fillable = [
        'workspace_id',
        'customer_id',
        'assignee_id',
        'status',
        'is_bot_paused',
        'bot_paused_until',
        'sentiment',
        'is_escalated',
        'escalation_reason',
        'notes',
        'tags',
    ];

    protected $casts = [
        'status'           => 'string',
        'is_bot_paused'    => 'boolean',
        'is_escalated'     => 'boolean',
        'bot_paused_until' => 'datetime',
        'tags'             => 'array',
    ];

    /**
     * Check if AI Bot is actively allowed to reply to this conversation.
     */
    public function isBotActive(): bool
    {
        if ($this->is_bot_paused) {
            if ($this->bot_paused_until && now()->greaterThan($this->bot_paused_until)) {
                // Timer expired, resume bot
                $this->update(['is_bot_paused' => false, 'bot_paused_until' => null]);
                return true;
            }
            return false;
        }

        return $this->status !== self::STATUS_HUMAN_HANDLING;
    }

    /**
     * Pause bot for human takeover.
     */
    public function pauseBot(?int $minutes = null): void
    {
        $this->update([
            'is_bot_paused'    => true,
            'bot_paused_until' => $minutes ? now()->addMinutes($minutes) : null,
            'status'           => self::STATUS_HUMAN_HANDLING,
        ]);
    }

    /**
     * Resume bot auto-replies.
     */
    public function resumeBot(): void
    {
        $this->update([
            'is_bot_paused'    => false,
            'bot_paused_until' => null,
            'status'           => self::STATUS_OPEN,
        ]);
    }

    /**
     * Enforce status constraint so invalid statuses fallback to STATUS_OPEN.
     */
    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = in_array($value, self::$validStatuses, true)
            ? $value
            : self::STATUS_OPEN;
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
