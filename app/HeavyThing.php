<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/8/18
 * Time: 7:04 PM
 */

namespace App;
use Illuminate\Database\Eloquent\Model;

class HeavyThing extends Model
{
    protected $fillable = ['status', 'price','name'];
    public function orders()
    {
        return $this->belongsToMany('App\HeavyThing', 'heavy_thing_order', 'heavy_thing_id', 'order_id')->withPivot('count');
    }
}
