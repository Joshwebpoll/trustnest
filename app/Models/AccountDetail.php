<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDetail extends Model
{
    protected $fillable = [
        'unique_bank_account_id',
        'bank_code',
        'bank_name',
        'account_number',
        'account_name',
        "user_id"
    ];
}
