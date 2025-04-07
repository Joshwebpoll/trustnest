<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpNotification extends Model
{


    protected $fillable = ['user_id', 'title', 'message', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
