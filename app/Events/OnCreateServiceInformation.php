<?php

namespace App\Events;

use App\News;
use App\Service;
use App\ServiceInformation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class OnCreateServiceInformation
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $service;
    public $serviceInformation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->serviceInformation = $service->serviceInformation()->firstOrFail();
        
        Log::warning('OnCreateServiceInformation event triggered for service: ' . $service->first());
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
