<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniqueBankAccount extends Model
{
    protected $fillable = [
        "bank_id",
        'contract_code',
        'account_reference',
        'account_name',
        'currency_code',
        'customer_email',
        'customer_name',
        'collection_channel',
        'reservation_reference',
        'reserved_account_type',
        'status',
        'created_on',
        'bvn',
        'restrict_payment_source',
        "user_id"

    ];
    // public function reservedAccount()
    // {
    //     return $this->belongsTo(UniqueBankAccount::class);
    // }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accounts()
    {
        return $this->hasMany(AccountDetail::class);
    }
}
