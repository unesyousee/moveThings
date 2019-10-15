<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function thirdparty()
    {
        return $this->belongsTo('App\Thirdparty');
    }

    public function discountUsages()
    {
        return $this->hasMany('App\DiscountUsage');
    }

    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

    public function carrierUsers()
    {
        return $this->belongsToMany('App\CarrierUser', 'carrier_user_order', 'order_id', 'carrier_user_id');
    }

    public function originAddress()
    {
        return $this->belongsTo('App\Address', 'origin_address_id');
    }

    public function destAddress()
    {
        return $this->belongsTo('App\Address', 'dest_address_id');
    }

    public function heavyThings()
    {
        return $this->belongsToMany('App\HeavyThing', 'heavy_thing_order', 'order_id', 'heavy_thing_id')->withPivot('count');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transactions');
    }

    public function comment()
    {
        return $this->hasOne('App\Comment');
    }

    public function statusLogs()
    {
        return $this->hasMany('App\StatusLog');
    }

    public function observer()
    {
        return $this->belongsTo('App\Observer');
    }

    public function tracker()
    {
        return $this->belongsTo('App\User', 'tracked');
    }

    public function events(){
        return $this->hasMany(\App\OrderEvent::class);
    }
}
