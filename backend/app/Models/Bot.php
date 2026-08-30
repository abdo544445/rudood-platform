<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Bot extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'name', 'system_prompt', 'model_type',
        'ai_provider', 'api_key', 'api_key_encrypted', 'api_base_url',
        'bot_tone', 'welcome_message', 'max_tokens', 'temperature', 'is_active',
        'enable_rag', 'enable_auto_rules', 'api_mode',
    ];

    protected $hidden = ['api_key_encrypted'];

    protected $casts = [
        'is_active'         => 'boolean',
        'enable_rag'        => 'boolean',
        'enable_auto_rules' => 'boolean',
        'temperature'       => 'float',
    ];

    /**
     * Store the API key encrypted.
     */
    public function setApiKeyAttribute(?string $value): void
    {
        if (!empty($value)) {
            $this->attributes['api_key_encrypted'] = Crypt::encryptString($value);
        } else {
            $this->attributes['api_key_encrypted'] = null;
        }
    }

    /**
     * Retrieve the API key decrypted.
     */
    public function getApiKeyAttribute(): ?string
    {
        if (empty($this->api_key_encrypted)) return null;
        try {
            return Crypt::decryptString($this->api_key_encrypted);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Relationships
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function autoRules()
    {
        return $this->hasMany(AutoRule::class, 'workspace_id', 'workspace_id');
    }

    public function knowledgeBases()
    {
        return $this->hasMany(KnowledgeBase::class);
    }

    /**
     * Get human-readable provider name.
     */
    public function getProviderLabelAttribute(): string
    {
        return match ($this->ai_provider) {
            'openai'            => 'OpenAI',
            'gemini'            => 'Google Gemini',
            'anthropic'         => 'Anthropic Claude',
            'openai_compatible' => 'OpenAI Compatible',
            default             => $this->ai_provider,
        };
    }
}
