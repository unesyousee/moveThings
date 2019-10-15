<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FcmRegisterId extends Model
{
    protected $table = 'fcm_register_ids';
    public function user(){
        return $this->belongsTo(User::class);
    }
}
