<?php

namespace App\Events;

use App\Client;
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
    public $client;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PositionCandidature $positionCandidature, Client $client)
    {
        $this->positionCandidature = $positionCandidature;
        $this->client = $client;
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
