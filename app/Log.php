<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded=[];
    protected $casts=['requests' => 'array'];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
