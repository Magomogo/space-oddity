<?php

namespace Acme\Pay\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Stash;

class CacheServiceProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        $app['cache-pool'] = $app->factory(function () {
            return new Stash\Pool(new Stash\Driver\Memcache());
        });
    }

    public function boot(Container $app)
    {
    }
}
