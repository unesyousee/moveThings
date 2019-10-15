<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotifMessage extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
