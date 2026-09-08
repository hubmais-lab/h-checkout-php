<?php

declare(strict_types=1);

namespace Hubmais\HCheckout\Providers;

use Illuminate\Support\ServiceProvider;

class HCheckoutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
       /** @noinspection PhpUndefinedMethodInspection */
        $this->mergeConfigFrom(__DIR__ . '/../config/h-checkout.php', 'h-checkout');

        $this->app->singleton(\Hubmais\HCheckout\Client::class, function () {
            $client = new \Hubmais\HCheckout\Client(
                config('h-checkout.endpoint'),
                config('h-checkout.timeout'),
            );

            $client->setToken(config('h-checkout.token'));
            $client->setMarketplaceId(config('h-checkout.marketplace_id'));
            $client->setSellerId(config('h-checkout.seller_id'));

            return $client;
        });

        $this->app->singleton(\Hubmais\HCheckout\Services\Manager::class, function ($app) {
            return new \Hubmais\HCheckout\Services\Manager($app->make(\Hubmais\HCheckout\Client::class));
        });

        $this->app->singleton('h-checkout', function ($app) {
            return $app->make(\Hubmais\HCheckout\Services\Manager::class);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/h-checkout.php' => config_path('h-checkout.php'),
        ], 'h-checkout-config');
    }
}