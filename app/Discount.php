<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/8/18
 * Time: 6:15 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $guarded = ['id'];

    public function discountUsages()
    {
        return $this->hasMany('App\DiscountUsage');
    }
    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }
}
