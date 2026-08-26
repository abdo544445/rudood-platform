<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'action',
        'category',
        'description',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to quickly record an audit log entry.
     */
    public static function record(string $action, string $description, ?string $category = 'system', ?array $metadata = null, ?int $workspaceId = null, ?int $userId = null): self
    {
        return self::create([
            'workspace_id' => $workspaceId ?? (auth()->check() ? auth()->user()->workspace_id : null),
            'user_id'      => $userId ?? (auth()->check() ? auth()->id() : null),
            'action'       => $action,
            'category'     => $category ?? 'system',
            'description'  => $description,
            'ip_address'   => request()->ip(),
            'metadata'     => $metadata,
        ]);
    }
}
