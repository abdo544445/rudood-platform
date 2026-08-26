<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    public const STATUS_OPEN           = 'open';
    public const STATUS_CLOSED_BY_BOT  = 'closed_by_bot';
    public const STATUS_HUMAN_HANDLING = 'human_handling';
    public const STATUS_CLOSED         = 'closed';
    public const STATUS_RESOLVED       = 'resolved';

    public static array $validStatuses = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED_BY_BOT,
        self::STATUS_HUMAN_HANDLING,
        self::STATUS_CLOSED,
        self::STATUS_RESOLVED,
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
        'context_summary',
        'notes',
        'tags',
        'csat_score',
        'csat_feedback',
        'resolved_at',
        'resolved_by',
        'is_converted',
        'conversion_revenue',
        'converted_at',
        'attributed_order_id',
    ];

    protected $casts = [
        'status'             => 'string',
        'is_bot_paused'      => 'boolean',
        'is_escalated'       => 'boolean',
        'bot_paused_until'   => 'datetime',
        'tags'               => 'array',
        'csat_score'         => 'integer',
        'resolved_at'        => 'datetime',
        'is_converted'       => 'boolean',
        'conversion_revenue' => 'decimal:2',
        'converted_at'       => 'datetime',
    ];

    /**
     * Mark conversation as converted with attributed order and revenue.
     */
    public function markAsConverted(float $revenue, int $orderId): void
    {
        $this->update([
            'is_converted'        => true,
            'conversion_revenue'  => $revenue,
            'converted_at'        => now(),
            'attributed_order_id' => $orderId,
        ]);
    }

    public function attributedOrder(): BelongsTo
    {
        return $this->belongsTo(MockOrder::class, 'attributed_order_id');
    }

    /**
     * Mark conversation as resolved and pause bot.
     */
    public function resolve(?string $resolvedBy = null): void
    {
        $this->update([
            'status'        => self::STATUS_RESOLVED,
            'resolved_at'   => now(),
            'resolved_by'   => $resolvedBy ?? 'agent',
            'is_bot_paused' => true,
        ]);
    }

    /**
     * Record customer satisfaction (CSAT) rating and feedback.
     */
    public function recordCsat(int $score, ?string $feedback = null): void
    {
        $this->update([
            'csat_score'    => max(1, min(5, $score)),
            'csat_feedback' => $feedback,
        ]);
    }

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
