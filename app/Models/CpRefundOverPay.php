<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpRefundOverPay extends Model
{
    protected $fillable = ['loan_id', 'user_id', 'repayment_amount', 'refund_amount', 'status', 'notes'];
}
