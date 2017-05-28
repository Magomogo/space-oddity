<?php

namespace Acme\Pay;

use Mockery as m;
use Acme\Pay\Test;

class WalletTest extends Test\WebTestCase
{
    public function testThereIsARouteForClientCreation()
    {
        $this->app['clients-service'] = m::mock(['getByName' => Test\Data::johnDoeFromSanFrancisco()]);
        $this->app['wallets-service'] = m::mock(['create' => (object)['id' => 42]]);

        $client = $this->createClient();
        $client->request('POST', '/client/John/wallet/USD?balance=2020000');

        $this->assertSame(201, $client->getResponse()->getStatusCode());
    }

    public function testDelegatesAClientCreationToClientsService()
    {
        $walletsService = m::mock();
        $walletsService->shouldReceive('create')->once()->andReturn((object)['id' => 42]);

        $this->app['wallets-service'] = $walletsService;
        $this->createClient()->request('POST', '/client/John/wallet/USD');
    }
}
