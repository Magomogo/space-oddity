<?php

namespace Acme\Pay;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RoutingTest extends WebTestCase
{

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../src/app.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }

    public function testIndexPageWorks()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains(
            'Welcome to ACME pay',
            $crawler->filter('h1')->text()
        );
    }
}
