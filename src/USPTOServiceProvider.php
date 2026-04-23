<?php

namespace RadicalDreamers\UsptoClient;

use Illuminate\Support\ServiceProvider;

class USPTOServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('uspto-client.php'),
            ], 'uspto-client-config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'uspto-client');

        $this->app->singleton(USPTOClient::class, static fn (): USPTOClient => new USPTOClient);
        $this->app->singleton(USPTOService::class, static fn ($app): USPTOService => new USPTOService($app->make(USPTOClient::class)));
    }
}
