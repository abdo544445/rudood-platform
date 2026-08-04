<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = ['company_name', 'plan_id', 'status'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function bots()
    {
        return $this->hasMany(Bot::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function autoRules()
    {
        return $this->hasMany(AutoRule::class);
    }
}
