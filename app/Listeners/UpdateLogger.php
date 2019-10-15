<?php

namespace App\Listeners;

use App\Events\OrderUpdateLog;
use App\OrderEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateLogger
{
    public function __construct()
    {
        //
    }

    public function handle(OrderUpdateLog $event)
    {
        $before = collect($event->getOrder()->getOriginal());
        $after = collect($event->getOrder()->getAttributes());
        $diff = $after->diffAssoc($before)->all();
        $changes = [];
        $count = 0;
        foreach ($diff as $key =>$value){
            $changes[$count] = [$before[$key] ,$value];
            $count++;
        }
        $changes = json_encode($changes);
        $model = new OrderEvent();
        $model->title =  $event->getString();
        $model->changes =   $changes;
        $model->user_id =  auth()->user()->id;
        $model->order_id =  $event->getOrder()->id;
        $model->save();
    }
}
