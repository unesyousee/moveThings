<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/8/18
 * Time: 6:33 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function carrierUser()
    {
        return $this->belongsTo('App\CarrierUser');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}