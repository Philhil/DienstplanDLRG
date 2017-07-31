<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserApproved implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $user;
    public $authorizedby;

    /**
     * Create a new event instance.
     *
     * @param User instance
     * @param User instance of authorized by
     * @return void
     */
    public function __construct(User $user, $authorizedby)
    {
        $this->user = $user;
        $this->authorizedby = $authorizedby;
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
