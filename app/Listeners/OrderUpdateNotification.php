<?php

namespace App\Listeners;

use App\Events\OrderUpdateEvent;
use App\Jobs\ProcessNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderUpdateNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderUpdateEvent  $event
     * @return void
     */
    public function handle(OrderUpdateEvent $event)
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
