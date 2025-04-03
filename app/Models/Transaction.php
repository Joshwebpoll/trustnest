<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [

        "transaction_id",
        'transaction_name',
        "transaction_amount",
        "transaction_type",
        "transaction_reference",
        "transaction_status"

    ];
}
