<?php

namespace Acme\Pay\Service;

use Acme\Pay\Test;
use Mockery as m;

class WalletsTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatesAWallet()
    {
        $db = m::mock(['lastInsertId' => 2]);
        $db->shouldReceive('insert')->once();

        $wallet = (new Wallets($db, m::mock()))->create(
            Test\Data::johnDoeFromSanFrancisco(42),
            'USD',
            1000
        );

        $this->assertSame(
            <<<JSON
{
    "id": 2,
    "client": {
        "name": "John Doe",
        "city": "San Francisco",
        "country": "USA",
        "id": 42
    },
    "currency": "USD",
    "balance": 1000
}
JSON
            ,
            json_encode($wallet, JSON_PRETTY_PRINT)
        );
    }

}
