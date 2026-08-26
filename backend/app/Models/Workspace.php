<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = [
        'company_name',
        'plan_id',
        'status',
        'webhook_secret',
        'allow_custom_api_key',
        'monthly_message_limit',
        'messages_used_this_month',
        'tokens_used_this_month',
    ];

    protected $casts = [
        'allow_custom_api_key'     => 'boolean',
        'monthly_message_limit'    => 'integer',
        'messages_used_this_month' => 'integer',
        'tokens_used_this_month'   => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function bots()
    {
        return $this->hasMany(Bot::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function autoRules()
    {
        return $this->hasMany(AutoRule::class);
    }

    public function cannedReplies()
    {
        return $this->hasMany(CannedReply::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if workspace has remaining message quota for this cycle.
     */
    public function hasRemainingQuota(): bool
    {
        if ($this->monthly_message_limit <= 0) {
            return true; // unlimited
        }
        return $this->messages_used_this_month < $this->monthly_message_limit;
    }

    /**
     * Increment usage counters.
     */
    public function recordUsage(int $messages = 1, int $tokens = 0): void
    {
        $this->increment('messages_used_this_month', $messages);
        if ($tokens > 0) {
            $this->increment('tokens_used_this_month', $tokens);
        }
    }

    /**
     * Get usage percentage (0-100).
     */
    public function getUsagePercentageAttribute(): int
    {
        if ($this->monthly_message_limit <= 0) return 0;
        return (int) min(100, round(($this->messages_used_this_month / $this->monthly_message_limit) * 100));
    }
}
