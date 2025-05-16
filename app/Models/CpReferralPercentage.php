<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpReferralPercentage extends Model
{
    protected $fillable = [
        'min_amount',
        'max_amount',
        'referral_reward_percent',
    ];
}
