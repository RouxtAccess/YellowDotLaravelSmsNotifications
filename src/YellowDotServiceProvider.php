<?php

namespace RouxtAccess\YellowDotNotifications;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use RouxtAccess\YellowDotNotifications\Channels\YellowDotChannel;

class YellowDotServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/yellowdot.php' => config_path('yellowdot.php')
        ], 'laravel-yellowdot');
    }

    public function register()
    {
        Notification::extend('yellow_dot', function ($app) {
            return new YellowDotChannel();
        });
    }
}
