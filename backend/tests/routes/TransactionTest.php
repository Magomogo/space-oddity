<?php

namespace Acme\Pay\Routes;

use Mockery as m;
use Acme\Pay\Test;

class TransactionTest extends Test\WebTestCase
{
    public function testProvidesListOfTransactionsAsJson()
    {
        $this->app['clients-service'] = m::mock(['getByName' => Test\Data::johnDoeFromSanFrancisco()]);
        $this->app['transactions-service'] = m::mock(['sortedList' => [
            ['id' => 1]
        ]]);

        $client = $this->createClient();
        $client->request('GET', '/client/John/wallet/transactions?limit=10&offset=0');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJson($client->getResponse()->getContent());
    }
}
