<?php

namespace Acme\Pay\Routes;

use Mockery as m;
use Acme\Pay\Test;

class CurrencyTest extends Test\WebTestCase
{
    public function testThereIsARouteForClientCreation()
    {
        $this->app['currencies-service'] = m::mock(['defineRate' => true]);

        $client = $this->createClient();
        $client->request('POST', '/currency/EUR/rate/1.1184?date=2017-09-09');

        $this->assertSame(201, $client->getResponse()->getStatusCode());
    }

    public function testCurrencyCodeGetValidated()
    {
        $client = $this->createClient();
        $client->request('POST', '/currency/UUU/rate/0.01');

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }

    public function testRateShouldBeNotZero()
    {
        $client = $this->createClient();
        $client->request('POST', '/currency/RUB/rate/0');

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }

    public function testDateIsValidated()
    {
        $client = $this->createClient();
        $client->request('POST', '/currency/EUR/rate/1.1184?date=20s17--0-');

        $this->assertSame(400, $client->getResponse()->getStatusCode());
    }

    public function testDelegatesActionToDedicatedService()
    {
        $service = m::mock();
        $service
            ->shouldReceive('defineRate')
            ->with('EUR', 1.1184, m::type('string'))
            ->once();

        $this->app['currencies-service'] = $service;

        $this->createClient()->request('POST', '/currency/EUR/rate/1.1184');
    }
}
