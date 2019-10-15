<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thirdparty extends Model
{
    protected $guarded =[];
    public function orders(){
        return $this->hasMany('App\Order');
    }
    public function transactions(){
        return $this->hasMany('App\Transaction', 'user_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
