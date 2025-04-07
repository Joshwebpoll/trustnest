<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpLoan extends Model
{
    protected $fillable = [
        'user_id',
        'loan_number',
        'amount',
        'interest_rate',
        'duration_months',
        'monthly_repayment',
        'total_payable',
        "remaining_balance",
        "guarantor_user_id",
        "guarantor_name",
        "guarantor_email",
        'status',
        'start_date',
        "end_date",
        'application_date',
        'approved_by',
        'approved_at',
        'purpose'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repayments()
    {
        return $this->hasMany(CpRepayment::class);
    }
}
