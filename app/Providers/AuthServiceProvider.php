<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('administration', function($user){
            return $user->isAdmin();
        });

        Gate::define('superadministration', function($user){
            return $user->isSuperAdmin();
        });

        Gate::define('trainingeditor', function($user){
            return $user->isTrainingEditor();
        });

        Gate::define('statisticeditor', function($user){
            return $user->isStatisticEditor();
        });
    }
}
