<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Não precisamos mexer aqui por agora
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // FORÇA HTTPS EM PRODUÇÃO (Railway, proxies, load balancers)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
