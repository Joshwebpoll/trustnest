<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NinVerification extends Model
{
    protected $fillable = [
        'nin',
        'last_name',
        'first_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'mobile_number',
    ];
}
