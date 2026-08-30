<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role', 'workspace_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->role === 'admin';
    }

    /**
     * Get the effective workspace ID for this user.
     * For Super Admins, checks session-based workspace switch first,
     * then falls back to the DB column. This prevents permanent DB mutation
     * when admins browse different tenant stores.
     */
    public function getEffectiveWorkspaceIdAttribute(): ?int
    {
        if ($this->isSuperAdmin() && session()->has('admin_active_workspace_id')) {
            return (int) session('admin_active_workspace_id');
        }
        return $this->workspace_id;
    }
}
