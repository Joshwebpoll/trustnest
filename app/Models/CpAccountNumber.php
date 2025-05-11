<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpAccountNumber extends Model
{
    protected $fillable = ["bank_account_name", 'bank_account_number', 'bank_name', "bank_code", 'user_id', 'country_code', 'currency_code', 'status'];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
