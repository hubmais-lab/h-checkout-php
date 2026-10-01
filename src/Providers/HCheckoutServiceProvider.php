<?php

declare(strict_types=1);

namespace Hubmais\HCheckout\Providers;

use Illuminate\Support\ServiceProvider;

class HCheckoutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
       /** @noinspection PhpUndefinedMethodInspection */
        $this->app->singleton(\Hubmais\HCheckout\Services\Manager::class, function ($app) {
            return new \Hubmais\HCheckout\Services\Manager($app->make(\Hubmais\HClient\Client::class));
        });

        $this->app->singleton('h-checkout', function ($app) {
            return $app->make(\Hubmais\HCheckout\Services\Manager::class);
        });
    }

    public function boot(): void
    {
        
    }
}