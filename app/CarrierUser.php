<?php
/**
 * Created by PhpStorm.
 * User: hassan
 * Date: 10/8/18
 * Time: 6:30 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class CarrierUser extends Model
{
    protected $fillable = ['national_code'];
    protected $table='carrier_user';
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function carrier()
    {
        return $this->belongsTo('App\Carrier');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function orders()
    {
        return $this->belongsToMany('App\Order', 'carrier_user_order', 'carrier_user_id', 'order_id');
    }
    public function parent()
    {
        return $this->belongsTo('App\CarrierUser', 'parent_id');
    }

    public function ratingPenalty()
    {
        return $this->hasMany(RatingPenalty::class);
    }
}