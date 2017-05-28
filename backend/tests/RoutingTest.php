<?php

namespace Acme\Pay;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Mockery as m;

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

    public function testThereIsARouteForClientCreation()
    {
        $this->app['clients-service'] = m::mock(['newClient' => true]);

        $client = $this->createClient();
        $client->request('POST', '/client', [], [], [], '{"name": "John Doe"}');

        $this->assertSame(201, $client->getResponse()->getStatusCode());
    }

    public function testIncomingClientJsonIsValidated()
    {
        $client = $this->createClient();
        $client->request('POST', '/client', [], [], [], '{"not-a": "client"}');

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }
}
