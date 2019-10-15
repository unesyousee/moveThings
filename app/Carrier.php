<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function carrierUsers()
    {
        return $this->hasMany('App\CarrierUser');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
