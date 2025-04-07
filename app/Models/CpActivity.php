<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpActivity extends Model
{
    protected $fillable = ['user_id', 'action',  'target_type', 'browser_type', 'target_id', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
