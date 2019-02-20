<?php

namespace App\Events;

use App\Qualification;
use App\Qualification_user;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class QualificationAssigned implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $qualification_user;
    public $authorizedby;
    public $client;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Qualification_user $qualification_user, User $authorizedby)
    {
        $this->qualification_user = $qualification_user;
        $this->authorizedby = $authorizedby;
        $this->client = $authorizedby->currentclient();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
