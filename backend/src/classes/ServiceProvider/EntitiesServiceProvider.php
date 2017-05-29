<?php

namespace Acme\Pay\ServiceProvider;

use Acme\Pay\Service;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EntitiesServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app['clients-service'] = $app->factory(function () use ($app) {
            return new Service\Clients($app['db']);
        });
        $app['wallets-service'] = $app->factory(function () use ($app) {
            return new Service\Wallets($app['db'], $app['currencies-service']);
        });
        $app['currencies-service'] = $app->factory(function () use ($app) {
            return new Service\Currencies($app['db']);
        });
    }

    public function boot(Container $app)
    {
    }
}
