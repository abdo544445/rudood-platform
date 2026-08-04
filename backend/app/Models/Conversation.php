<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['workspace_id', 'customer_id', 'assignee_id', 'status'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
