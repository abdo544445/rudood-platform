<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsSnapshot extends Model
{
    protected $fillable = [
        'workspace_id',
        'period_key',
        'total_conversations',
        'ai_resolved_conversations',
        'deflection_rate',
        'hours_saved',
        'cost_savings_amount',
        'revenue_generated',
        'converted_orders_count',
    ];

    protected $casts = [
        'deflection_rate'        => 'decimal:2',
        'hours_saved'            => 'decimal:2',
        'cost_savings_amount'    => 'decimal:2',
        'revenue_generated'      => 'decimal:2',
        'total_conversations'    => 'integer',
        'ai_resolved_conversations' => 'integer',
        'converted_orders_count' => 'integer',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
