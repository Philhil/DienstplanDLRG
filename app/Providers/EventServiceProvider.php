<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\PositionAuthorized' => [
            'App\Listeners\PositionAuthorized',
        ],

        'App\Events\ServiceDelete' => [
            'App\Listeners\PositionDelete',
        ],

        'App\Events\OnCreateNews' => [
            'App\Listeners\OnCreateNews',
        ],

        'App\Events\QualificationAssigne' => [
            'App\Listeners\QualificationAssignedListener',
        ],

        'App\Events\UserApproved' => [
            'App\Listeners\UserApproved',
        ],

        'App\Events\UserRegistered' => [
            'App\Listeners\UserRegisteredListener',
        ],

/*        'App\Events\NewPositioncandidature' => [
            'App\Listeners\NewPositioncandidatureListener',
        ],*/
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
