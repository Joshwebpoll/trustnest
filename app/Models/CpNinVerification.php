<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class CpNinVerification extends Model
{
    protected $fillable = [
        'nin',
        'last_name',
        'first_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'mobile_number',
        "user_id"
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
