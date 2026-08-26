<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Channel extends Model
{
    protected $fillable = [
        'workspace_id', 'platform', 'label',
        'access_token', 'phone_number_id', 'verify_token',
        'bot_token', 'bot_username', 'chat_id', 'webhook_url',
        'instagram_account_id', 'page_access_token', 'auto_reply_comments', 'comment_reply_template',
        'widget_color', 'widget_position', 'widget_greeting',
        'is_connected', 'is_active', 'last_error', 'connected_at',
    ];

    protected $hidden = ['access_token', 'verify_token', 'bot_token', 'page_access_token'];

    protected $casts = [
        'is_connected'        => 'boolean',
        'is_active'           => 'boolean',
        'auto_reply_comments' => 'boolean',
        'connected_at'        => 'datetime',
    ];

    // ─── Encrypted token storage ──────────────────────────────────────────
    public function setAccessTokenAttribute(?string $value): void
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getAccessTokenAttribute(): ?string
    {
        return $this->decrypt($this->attributes['access_token'] ?? null);
    }

    public function setVerifyTokenAttribute(?string $value): void
    {
        $this->attributes['verify_token'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getVerifyTokenAttribute(): ?string
    {
        return $this->decrypt($this->attributes['verify_token'] ?? null);
    }

    public function setBotTokenAttribute(?string $value): void
    {
        $this->attributes['bot_token'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getBotTokenAttribute(): ?string
    {
        return $this->decrypt($this->attributes['bot_token'] ?? null);
    }

    public function setPageAccessTokenAttribute(?string $value): void
    {
        $this->attributes['page_access_token'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getPageAccessTokenAttribute(): ?string
    {
        return $this->decrypt($this->attributes['page_access_token'] ?? null);
    }

    public function isActive(): bool
    {
        return $this->is_connected && ($this->is_active ?? true);
    }

    private function decrypt(?string $value): ?string
    {
        if (!$value) return null;
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function getProviderLabelAttribute(): string
    {
        return match ($this->platform) {
            'whatsapp' => 'WhatsApp Cloud API',
            'telegram' => 'Telegram Bot',
            'instagram'=> 'Instagram',
            'web'      => 'Web Widget',
            default    => ucfirst($this->platform),
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->platform) {
            'whatsapp' => 'bi-whatsapp',
            'telegram' => 'bi-send',
            'instagram'=> 'bi-instagram',
            'web'      => 'bi-globe2',
            default    => 'bi-plug',
        };
    }
}
