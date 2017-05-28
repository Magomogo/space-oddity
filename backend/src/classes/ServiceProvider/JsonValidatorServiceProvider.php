<?php

namespace Acme\Pay\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Acme\Pay\Service\JsonValidator;

class JsonValidatorServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app['data-types-validator'] = $app->factory(function () {
            return new JsonValidator();
        });
    }

    public function boot(Container $app)
    {
    }
}
