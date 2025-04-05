<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    protected $fillable = [
        "transaction_id",
        "account_number",
        "amount_deposited",
        'saving_type',
        "status",
        'transaction_reference',
        "deposit_type",
        "processed_by",
        "deposit_date"

    ];
}
