<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CannedReply extends Model
{
    protected $fillable = [
        'workspace_id',
        'shortcut',
        'title',
        'content',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
