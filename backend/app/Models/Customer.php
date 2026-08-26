<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['workspace_id', 'name', 'phone', 'chat_id', 'email', 'platform'];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Returns the first two characters of the name as an avatar placeholder.
     */
    public function getAvatarAttribute(): string
    {
        return mb_substr($this->name, 0, 2);
    }
}
