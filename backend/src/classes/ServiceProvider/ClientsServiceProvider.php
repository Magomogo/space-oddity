<?php

namespace Acme\Pay\ServiceProvider;

use Acme\Pay\Service\Clients;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ClientsServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app['clients-service'] = $app->factory(function () {
            return new Clients();
        });
    }

    public function boot(Container $app)
    {
    }
}
