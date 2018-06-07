<?php

namespace LivePersonNY\LiveEngageLaravel;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/live-engage-laravel.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('live-engage-laravel.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'live-engage-laravel'
        );

        $this->app->bind('live-engage-laravel', function () {
            return new LiveEngageLaravel();
        });
    }
}
