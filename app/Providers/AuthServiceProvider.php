<?php

namespace App\Providers;

use App\Extensions\WechatMiniProgramGuard;
use App\Models\Players;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

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
        $this->registerPolicies();

        Auth::provider('players', function () {
            return new Players();
        });

        Auth::extend('webchat-mp', function ($app, $name, array $config) {
            $request = $app->make('request');
            $session = $app->make('session');
            return new WechatMiniProgramGuard($name, Auth::createUserProvider($config['provider']), $request, $session);
        });
    }
}
