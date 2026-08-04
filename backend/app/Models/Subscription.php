<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['workspace_id', 'plan_name', 'price', 'status', 'renews_at'];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
