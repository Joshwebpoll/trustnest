<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonnifyPaymentTransaction extends Model
{
    protected $fillable = [
        'transaction_reference',
        'payment_reference',
        'payment_description',
        'payment_method',
        'amount_paid',
        'total_payable',
        'settlement_amount',
        'currency',
        'payment_status',
        'customer_name',
        'customer_email',
        'bank_code',
        'amount_paid_from_bank',
        'account_name',
        'session_id',
        'account_number',
        'destination_bank_code',
        'destination_bank_name',
        'destination_account_number',
    ];
}
