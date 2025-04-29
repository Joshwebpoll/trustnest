<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CpContribution extends Model
{
    protected $fillable = [
        'member_id',
        "user_id",
        'transaction_id',
        'contribution_type',
        'amount_contributed',
        'reference_number',
        "account_number",
        'payment_method',
        'contribution_date',
        'status',
        'contribution_deposit_type',
        'processed_by_id',
        'processed_by_name',
        'processed_by_email'

    ];
    // 👇 this will expose the decrypted value
    //protected $appends = ['decrypted_contributed'];

    // 👇 this will hide the encrypted value (optional)
    // protected $hidden = ['amount_contributed'];
    public function getBalanceAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null; // or handle it however you like
        }
    }

    // Append the decrypted amount_contributed to the model output
    protected $appends = ['decrypted_amount_contributed'];

    // Optionally hide the encrypted field in JSON
    protected $hidden = ['amount_contributed'];

    // Accessor for decrypted amount_contributed
    public function getDecryptedAmountContributedAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['amount_contributed']);
        } catch (\Exception $e) {
            return 'Decryption Error'; // Handle decryption error
        }
    }
}
