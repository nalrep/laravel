<?php

namespace Nalrep;

use Illuminate\Support\ServiceProvider;

class NalrepServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nalrep.php', 'nalrep');

        $this->app->singleton('nalrep', function ($app) {
            return new NalrepManager($app);
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nalrep');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/nalrep.php' => config_path('nalrep.php'),
            ], 'config');
        }
    }
}
