<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/8/18
 * Time: 6:15 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class DiscountUsage extends Model
{
    protected $guarded = ['id'];
    public function discount()
    {
        return $this->belongsTo('App\Discount');
    }

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }
}
