<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/8/18
 * Time: 6:45 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}