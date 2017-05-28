<?php

namespace Acme\Pay\Service;

use Mockery as m;

class CurrenciesTest extends \PHPUnit_Framework_TestCase
{
    public function testWritesRateForADate()
    {
        $db = m::mock();
        $db->shouldReceive('insert')->with('currency_rates', [
            'date' => '2017-09-09',
            'code' => 'RUR',
            'rate' =>  0.017694
        ])->once();

        (new Currencies($db))->defineRate('RUR', 0.017694, '2017-09-09');
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
                ['code' => 'RUR', 'rate' => 56.57]
            ]
        ]);
        $amountInEur = (new Currencies($db))->convert(10000, 'RUR', 'EUR');

        $this->assertSame(158, $amountInEur);
    }
}
