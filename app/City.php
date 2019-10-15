<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function polygonNodes()
    {
        return $this->hasMany('App\PolygonNode');
    }
}