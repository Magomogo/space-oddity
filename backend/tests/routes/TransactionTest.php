<?php

namespace Acme\Pay\Routes;

use Mockery as m;
use Acme\Pay\Test;
use Acme\Pay\Test\Data;

class TransactionTest extends Test\WebTestCase
{
    public function testProvidesListOfTransactionsAsJson()
    {
        $this->app['clients-service'] = m::mock(['getByName' => Test\Data::johnDoeFromSanFrancisco()]);
        $this->app['transactions-service'] = m::mock(['sortedList' => [
            Data::tenDollarsTransfer()
        ]]);

        $client = $this->createClient();
        $client->request(
            'GET',
            '/client/John/wallet/transactions',
            [],
            [],
            ['HTTP_ACCEPT' => 'application/json']
        );

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testProvidesListOfTransactionsAsCsv()
    {
        $this->app['clients-service'] = m::mock(['getByName' => Test\Data::johnDoeFromSanFrancisco()]);
        $this->app['transactions-service'] = m::mock(['sortedList' => [
            Data::tenDollarsTransfer()
        ]]);

        $client = $this->createClient();
        $client->request(
            'GET',
            '/client/John/wallet/transactions',
            [],
            [],
            ['HTTP_ACCEPT' => 'text/csv']
        );

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('text/csv; charset=UTF-8', $client->getResponse()->headers->get('content-type'));
    }
}
