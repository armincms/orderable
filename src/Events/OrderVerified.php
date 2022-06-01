<?php

namespace Armincms\Orderable\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Armincms\Orderable\Models\OrderableOrder;

class OrderVerified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The order instance.
     * 
     * @var \Armincms\Orderable\Models\OrderableOrder
     */
    public $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(OrderableOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('channel-name');
    }
}
