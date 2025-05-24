<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpRepayment extends Model
{
    protected $fillable = ['loan_id', 'user_id', 'repayment_amount', "remaining_balance", 'transaction_reference', 'payment_method', 'interest_component', 'due_date', 'repayment_date', 'status', 'comment', "updatedById", "updatedEmail", 'updatedName', 'interest_paid'];

    protected $casts = [
        'repayment_date' => 'date',
        'due_date' => 'date',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loan()
    {
        return $this->belongsTo(CpLoan::class);
    }
}
