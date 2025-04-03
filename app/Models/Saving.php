<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    protected $fillable = [
        "saving_id",
        "amount_deposited",
        'saving_type',
        'savingtransaction_id'
    ];
}
