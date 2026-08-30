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
     * Get the workspace ID this user is currently operating against.
     * For Super Admins who have an active "switch workspace" session override,
     * this returns the switched-to workspace instead of their own home workspace.
     * IMPORTANT: reads $value directly (not $this->workspace_id) to avoid infinite
     * recursion — this method IS the workspace_id accessor.
     */
    public function getWorkspaceIdAttribute($value): ?int
    {
        if ($this->isSuperAdmin() && session()->has('admin_active_workspace_id')) {
            return (int) session('admin_active_workspace_id');
        }
        return $value !== null ? (int) $value : null;
    }
}
