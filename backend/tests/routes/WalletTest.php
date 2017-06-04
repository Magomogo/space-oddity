<?php

namespace Acme\Pay\Routes;

use Mockery as m;
use Acme\Pay\Test;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WalletTest extends Test\WebTestCase
{
    public function testThereIsARouteForWalletCreation()
    {
        $this->app['clients-service'] = m::mock(['getByName' => Test\Data::johnDoeFromSanFrancisco()]);
        $this->app['wallets-service'] = m::mock(['create' => (object)['id' => 42]]);

        $client = $this->createClient();
        $client->request('POST', '/client/John/wallet/USD?balance=2020000');

        $this->assertSame(201, $client->getResponse()->getStatusCode());
    }

    public function testDelegatesAWalletCreationToWalletService()
    {
        $walletsService = m::mock();
        $walletsService->shouldReceive('create')->once()->andReturn((object)['id' => 42]);

        $this->app['clients-service'] = m::mock(['getByName' => Test\Data::johnDoeFromSanFrancisco()]);
        $this->app['wallets-service'] = $walletsService;
        $this->createClient()->request('POST', '/client/John/wallet/USD');
    }

    public function testTransferRouteIsExists()
    {
        $transactionService = m::mock();
        $transactionService->shouldReceive('create')->once();
        $this->app['transactions-service'] = $transactionService;

        $client = $this->createClient();
        $client->request('PUT', '/wallet/1/transfer-to/2/amount/10000?currency=own');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testTransferAmountShouldNotBeZero()
    {
        $client = $this->createClient();
        $client->request('PUT', '/wallet/1/transfer-to/2/amount/0');

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }

    public function testTransferAmountShouldBePositive()
    {
        $client = $this->createClient();
        $client->request('PUT', '/wallet/1/transfer-to/2/amount/-100');

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }

    public function testCurrencyParameterGetsValidated()
    {
        $this->app['transactions-service'] = m::mock(['create' => null]);

        $client = $this->createClient();
        $client->request('PUT', '/wallet/1/transfer-to/2/amount/10000?currency=their');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $client = $this->createClient();
        $client->request('PUT', '/wallet/1/transfer-to/2/amount/10000?currency=mine');
        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }

    public function testTransferShouldBeMadeBetweenDifferentWallets()
    {
        $client = $this->createClient();
        $client->request('PUT', '/wallet/1/transfer-to/1/amount/10000');
        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }
}
