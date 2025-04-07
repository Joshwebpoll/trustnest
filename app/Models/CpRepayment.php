<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpRepayment extends Model
{
    protected $fillable = ['loan_id', 'user_id', 'amount', 'repayment_date', 'status'];

    protected $casts = [
        'repayment_date' => 'date',
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
