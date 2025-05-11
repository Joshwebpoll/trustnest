<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpBankName extends Model
{
    protected $fillable = ["bank_name", 'bank_code', 'bank_type', 'country_code', 'currency_code', 'status', 'payment_gate_type', 'currency_code'];
}
