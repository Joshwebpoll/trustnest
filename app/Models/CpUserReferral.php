<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpUserReferral extends Model
{
    protected $fillable = ['referrer_id', "referred_user_id", "asUserContributed", "reward_amount", "status", 'paid_at'];

    protected $casts = [
        'paid_at' => 'datetime',


    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
