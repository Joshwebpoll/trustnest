<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpDividend extends Model
{
    protected $fillable = ['user_id', 'year', 'amount', 'note', 'distributed_at'];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
