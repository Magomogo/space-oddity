<?php

namespace Acme\Pay\ServiceProvider;

use Acme\Pay\Service\Clients;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ClientsServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app['clients-service'] = $app->factory(function () use ($app) {
            return new Clients($app['db']);
        });
    }

    public function boot(Container $app)
    {
    }
}
