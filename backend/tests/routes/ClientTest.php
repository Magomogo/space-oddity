<?php

namespace Acme\Pay;

use Mockery as m;

class ClientTest extends Test\WebTestCase
{
    public function testThereIsARouteForClientCreation()
    {
        $this->app['clients-service'] = m::mock(['create' => true]);

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

    public function testDelegatesAClientCreationToClientsService()
    {
        $clientsService = m::mock();
        $clientsService->shouldReceive('create')->once();

        $this->app['clients-service'] = $clientsService;
        $this->createClient()->request('POST', '/client', [], [], [], '{"name": "John Doe"}');
    }

    public function testListOfClientsRouteSomehowWorks()
    {
        $client = $this->createClient();
        $client->request('GET', '/client');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
