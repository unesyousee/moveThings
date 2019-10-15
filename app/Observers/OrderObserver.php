<?php

namespace App\Observers;

use App\DiscountUsage;
use App\Events\OrderUpdating;
use App\Order;
use App\Transaction;
use App\User;

class OrderObserver
{

    public function saved(Order $order){
        if ($order->status == 6){
            $discount_usage = DiscountUsage::where('user_id', $order->user_id)->where('share_code','!=',null)->first();
            if ($discount_usage != null){
                $orders = Order::where('user_id',$order->user_id)->where('id','!=',$order->id)->where('status',6)->get();
                if (sizeof($orders) == 0){
                    $user = User::where('share_code',$discount_usage->share_code)->first();
                    $trans = new Transaction;
                    $trans->user_id = $user->id;
                    $trans->description = 'کد تخفیف';
                    $trans->amount = $order->price * 0.05;
                    $trans->status = 1;
                    $trans->save();
                }
            }
        }
    }
    /**
     * Handle the order "created" event.
     *
     * @param  \App\Order  $order
     * @return void
     */

    public function created(Order $order)
    {
    }

    public function updating(Order $order)
    {
        event(new OrderUpdating($order));
    }
    /**
     * Handle the order "updated" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {

    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {

    }

    /**
     * Handle the order "restored" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
