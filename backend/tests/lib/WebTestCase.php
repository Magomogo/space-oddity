<?php

namespace Acme\Pay\Test;

use Symfony\Component\HttpKernel\HttpKernelInterface;

class WebTestCase extends \Silex\WebTestCase
{

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../src/app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }
}
