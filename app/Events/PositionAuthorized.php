<?php

namespace App\Events;

use App\Position;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class PositionAuthorized implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $position;
    public $authorizedby;
    /**
     * Create a new event instance.
     *
     * @param Position instance
     * @param User instance of authorized by
     * @return void
     */
    public function __construct(Position $position, $authorizedby)
    {
        $this->position = $position;
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
