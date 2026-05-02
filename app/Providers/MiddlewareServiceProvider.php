<?php

namespace App\Providers;

use App\Http\Middleware\CheckRole;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        // Register route middleware
        $router->aliasMiddleware('role', CheckRole::class);
    }
}
