<?php

namespace App\Events;

use App\PositionCandidature;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewPositioncandidature
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $positionCandidature;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PositionCandidature $positionCandidature)
    {
        $this->positionCandidature = $positionCandidature;
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
