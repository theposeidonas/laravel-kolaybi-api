<?php

namespace Theposeidonas\Kolaybi;

use Illuminate\Support\ServiceProvider;

class KolaybiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kolaybi.php', 'kolaybi');

        $this->app->singleton(KolaybiClient::class, function ($app) {
            return new KolaybiClient(config('kolaybi'));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kolaybi.php' => config_path('kolaybi.php'),
            ], 'config');
        }
    }
}
