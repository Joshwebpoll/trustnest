<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpInterestRate extends Model
{
    protected $fillable = [
        'min_amount',
        'max_amount',
        'interest_rate',
        'loan_duration'
    ];
}
