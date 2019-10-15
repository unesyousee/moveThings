<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/8/18
 * Time: 7:01 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function discountUsage()
    {
        return $this->belongsTo('App\DiscountUsage');
    }

    public function order()
    {
        return $this->belongsTo('App\Order');
    }
    public function operator()
    {
        return $this->belongsTo('App\user', 'operator_id');
    }
    public function setOpratorIdAttribute(){
        $this->oprator_id = Auth::user()->id;
    }
}
