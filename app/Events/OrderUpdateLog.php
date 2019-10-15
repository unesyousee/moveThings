<?php

namespace App\Events;

use App\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderUpdateLog
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $order;
    private $string;
    /**
     * Create a new event instance.
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }
    public function getString(){
        return $this->string;
    }
    public function __construct($order, $string)
    {
        $this->order = $order;
        $this->string = $string;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
