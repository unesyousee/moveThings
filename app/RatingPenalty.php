<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RatingPenalty extends Model
{
    protected $table = "rating_penalty";

    public function carrierUser(){
        return $this->belongsTo(CarrierUser::class);
    }
}
