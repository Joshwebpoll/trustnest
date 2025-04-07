<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpContribution extends Model
{
    protected $fillable = [
        'member_id',
        'transaction_id',
        'contribution_type',
        'amount_contributed',
        'reference_number',
        "account_number",
        'payment_method',
        'contribution_date',
        'status',
        'contribution_deposit_type',
        'processed_by'

    ];
}
