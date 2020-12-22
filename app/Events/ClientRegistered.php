<?php

namespace App\Events;

use App\Client;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ClientRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $client;
    public $order;

    /**
     * Create a new event instance.
     *
     * @param Client: newly created client
     * @param User: inital user of Client (Admin)
     * @return void
     */
    public function __construct(Client $client, User $user, $order)
    {
        $this->user = $user;
        $this->client = $client;
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
