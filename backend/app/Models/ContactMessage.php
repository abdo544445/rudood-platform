<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'ip_address',
        'admin_notes',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new'         => 'جديدة',
            'in_progress' => 'قيد المتابعة',
            'resolved'    => 'تم الرد والحل',
            default       => 'غير محدد',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'new'         => 'bg-danger bg-opacity-20 text-danger border border-danger border-opacity-25',
            'in_progress' => 'bg-warning bg-opacity-20 text-gold border border-warning border-opacity-25',
            'resolved'    => 'bg-success bg-opacity-20 text-success border border-success border-opacity-25',
            default       => 'bg-secondary bg-opacity-20 text-white-50 border border-secondary border-opacity-25',
        };
    }
}
