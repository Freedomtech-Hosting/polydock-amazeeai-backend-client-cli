<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Client::class, function ($app, array $parameters = []) {
            $token = $parameters['token'] ?? null;
            
            return new Client(
                config('polydock.base_url'),
                $token
            );
        });
    }
}
