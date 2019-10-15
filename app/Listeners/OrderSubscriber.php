<?php
/**
 * Created by PhpStorm.
 * User: nobaar
 * Date: 4/29/19
 * Time: 11:25 AM
 */

namespace App\Listeners;


use App\Events\OrderUpdateEvent;
use App\Jobs\ProcessNotification;
use Illuminate\Events\Dispatcher;

class OrderSubscriber
{
    public function subscribe(Dispatcher $event)
    {
        $event->listen(OrderUpdateEvent::class,'App\Listeners\OrderSubscriber@driverNotification');
    }

    public function driverNotification(OrderUpdateEvent $event)
    {
        $driver = $event->getOrder()->carrierUsers()->where('parent_id',null)->get()->first();
        if($driver){
            $driver = $event->getOrder()->carrierUsers()->where('parent_id', null)->first()->user;
            ProcessNotification::dispatch([
                'title' => '📣 ویرایش سفارش 🚚',
                'body' => "سفارش شماره".' '. $event->getOrder()->id.' '.'در سامانه نوبار ویرایش شد جهت رویت تغییر رات به اپ نوبار مراجعه نمایید.',
            ], $driver)->onQueue('notification');
        }
    }
}