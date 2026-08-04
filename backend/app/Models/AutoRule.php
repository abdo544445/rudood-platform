<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoRule extends Model
{
    protected $fillable = ['workspace_id', 'keywords', 'trigger_condition', 'reply_template', 'is_active'];

    protected $casts = [
        'keywords' => 'array',
        'is_active' => 'boolean',
    ];
}
