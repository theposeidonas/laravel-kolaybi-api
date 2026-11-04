<?php

namespace Theposeidonas\Kolaybi;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class KolaybiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/kolaybi.php', 'kolaybi');

        $this->app->singleton(KolaybiClient::class, fn (Application $app): KolaybiClient => new KolaybiClient(
            (array) $app['config']->get('kolaybi', [])
        ));

        $this->app->alias(KolaybiClient::class, 'kolaybi');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/kolaybi.php' => $this->app->configPath('kolaybi.php'),
            ], ['kolaybi-config', 'config']);
        }
    }
}
