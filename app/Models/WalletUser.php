<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class WalletUser extends Model
{
    protected $fillable = ["wallet_id", "wallet_balance", "balanace_before", "balanace_after", "user_id"];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function setBalanceAttribute($value)
    // {
    //     $this->attributes['wallet_balance'] = Crypt::encrypt($value);
    // }

    // // Decrypt balance when retrieving
    // public function getBalanceAttribute($value)
    // {
    //     return $value ? Crypt::decrypt($value) : null;
    // }
}
