<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpMembers extends Model
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
