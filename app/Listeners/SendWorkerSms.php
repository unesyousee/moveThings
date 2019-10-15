<?php

namespace App\Listeners;

use App\Events\OrderUpdating;
use App\Jobs\ProcessSendSms;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWorkerSms
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  OrderUpdating  $event
     * @return void
     */
    public function handle(OrderUpdating $event)
    {
/*        $order = $event->getOrder();
        $dirty = $event->getOrder()->getDirty();
        if(isset($dirty['status']) && $dirty['status'] == 1){
            $this->packingSms($order);
        }elseif(isset($dirty['status']) && $dirty['status'] == 6){
            $this->layoutSms($order);
        }*/
    }

    /**
     * @param OrderUpdating $event
     * @param array $order
     * @param $carrierUser
     */
    private function packingSms($order): void
    {
        if ($order->user->phone != '09338931751') {
            ProcessSendSms::dispatch([
                'phone' => $order->user->phone,
                'templateId' => "3678",
                'parameterArray' => array(array('Parameter' => "DriverName", 'ParameterValue' => ' '))
            ])->onQueue('sms')->delay(now()->addMinutes(10));
        } else {
            ProcessSendSms::dispatch([
                'phone' => $order->receiver_phone,
                'templateId' => "3678",
                'parameterArray' => array(array('Parameter' => "DriverName", 'ParameterValue' =>' '))
            ])->onQueue('sms')->delay(now()->addMinutes(10));
        }
    }
    private function layoutSms($order): void
    {
        if ($order->user->phone != '09338931751') {
            ProcessSendSms::dispatch([
                'phone' => $order->user->phone,
                'templateId' => "3678",
                'parameterArray' => array(array('Parameter' => "DriverName", 'ParameterValue' => ' '))
            ])->onQueue('sms')->delay(now()->addMinutes(10));
        } else {
            ProcessSendSms::dispatch([
                'phone' => $order->receiver_phone,
                'templateId' => "3678",
                'parameterArray' => array(array('Parameter' => "DriverName", 'ParameterValue' =>' '))
            ])->onQueue('sms')->delay(now()->addMinutes(10));
        }
    }
}
