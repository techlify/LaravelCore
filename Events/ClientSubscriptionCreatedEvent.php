<?php

namespace Modules\LaravelCore\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Modules\LaravelCore\Entities\ClientSubscription;

class ClientSubscriptionCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subscription;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ClientSubscription $subscription)
    {
        $this->subscription = $subscription;
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
