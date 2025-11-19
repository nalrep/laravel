<?php

namespace Narlrep;

use Illuminate\Support\ServiceProvider;

class NarlrepServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/narlrep.php', 'narlrep');

        $this->app->singleton('narlrep', function ($app) {
            return new NarlrepManager($app);
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'narlrep');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/narlrep.php' => config_path('narlrep.php'),
            ], 'config');
        }
    }
}
