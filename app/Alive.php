<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alive extends Model
{
    protected $fillable = ["user_id"];
    protected $table= "user_alive";
    public function user(){
        return $this->belongsTo(User::class);
    }
}
