<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MockOrder extends Model
{
    protected $table = 'mock_orders';

    protected $fillable = [
        'workspace_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'status',
        'courier',
        'tracking_number',
        'items_summary',
        'total_amount',
        'estimated_delivery',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get Arabic status badge.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'preparing' => 'قيد التجهيز والتغليف',
            'shipped'   => 'تم الشحن وفي الطريق للعميل',
            'delivered' => 'تم التوصيل بنجاح',
            'cancelled' => 'ملغي',
            default     => 'قيد المتابعة',
        };
    }
}
