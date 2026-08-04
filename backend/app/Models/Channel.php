<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['workspace_id', 'platform', 'access_token', 'phone_number_id', 'is_connected'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
