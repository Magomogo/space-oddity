<?php

namespace Acme\Pay\Service;

use Mockery as m;
use Acme\Pay\Exception\CurrencyRateUndefined;

class CurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function testWritesRateForADate()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('currency_rates', [
            'date' => '2017-09-09',
            'code' => 'RUB',
            'rate' =>  0.017694
        ])->once();

        (new Currencies($db))->defineRate('RUB', 0.017694, '2017-09-09');
    }

    public function testCanConvertDollarsIntoEurs()
    {
        $db = m::mock([
            'fetchAll' => [
                ['code' => 'EUR', 'rate' => 0.894434],
                ['code' => 'USD', 'rate' => 1]
            ]
        ]);
        $amountInEur = (new Currencies($db))->convert(10000, 'USD', 'EUR');

        $this->assertSame(8944, $amountInEur);
    }

    public function testCanConvertRublesIntoEurs()
    {
        $db = m::mock([
            'fetchAll' => [
                ['code' => 'EUR', 'rate' => 0.894434],
                ['code' => 'RUB', 'rate' => 56.57]
            ]
        ]);
        $amountInEur = (new Currencies($db))->convert(10000, 'RUB', 'EUR');

        $this->assertSame(158, $amountInEur);
    }

    public function testThrowsWhenRatesAreUndefined()
    {
        $this->expectException(CurrencyRateUndefined::class);

        (new Currencies(m::mock(['fetchAll' => []])))->convert(10000, 'RUB', 'EUR');
    }

    public function testUsesProvidedCacheObject()
    {
        $cache = m::mock([
            'getItem' => m::mock([
                'expiresAfter' => null,
                'get' => [
                    'EUR' => 0.894434,
                    'RUB' => 56.57
                ],
                'isHit' => true
            ])
        ]);

        (new Currencies(m::mock(), $cache))->convert(10000, 'RUB', 'EUR');
    }
}
