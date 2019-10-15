<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable=['path','user_id','uuid'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
