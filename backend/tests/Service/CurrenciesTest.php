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
}
