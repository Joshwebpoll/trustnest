<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CpMember extends Model
{
    protected $fillable = [
        'user_id',
        'membership_number',
        'full_name',
        'id_number',
        'phone',
        'email',
        'joining_date',
        'status',
        'total_shares',
        'total_savings',
    ];

    // Append the decrypted fields to the model output
    // protected $appends = ['decrypted_total_shares', 'decrypted_total_savings'];

    // // Optionally hide the encrypted fields in JSON response
    // protected $hidden = ['total_shares', 'total_savings'];

    // // Accessor for decrypted total_shares
    // public function getDecryptedTotalSharesAttribute()
    // {
    //     try {
    //         return Crypt::decryptString($this->attributes['total_shares']);
    //     } catch (\Exception $e) {
    //         return 'Decryption Error'; // Handle decryption error
    //     }
    // }

    // Accessor for decrypted total_savings
    // public function getDecryptedTotalSavingsAttribute()
    // {
    //     try {
    //         return Crypt::decryptString($this->attributes['total_savings']);
    //     } catch (\Exception $e) {
    //         return 'Decryption Error'; // Handle decryption error
    //     }
    // }

    // Relationship (if needed)
    // public function contributions()
    // {
    //     return $this->hasMany(CpContribution::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
