<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception\TransferPathIsNotFound;
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
    "client": {
        "name": "John Doe",
        "city": "San Francisco",
        "country": "USA",
        "id": 42
    },
    "currency": "USD",
    "balance": 1000,
    "id": 2
}
JSON
            ,
            json_encode($wallet, JSON_PRETTY_PRINT)
        );
    }

    public function testDoesTransfer()
    {
        $db = m::mock(['fetchColumn' => 'EUR', 'lastInsertId' => 88]);
        $db->shouldReceive('transactional')->with(m::on(function ($arg) { $arg(); return true; }));
        $db->shouldReceive('insert')->with('transaction', m::any())->ordered()->once();
        $db->shouldReceive('insert')->with('transfer', m::any())->ordered()->twice();
        $db->shouldReceive('executeUpdate')->ordered()->twice();

        (new Wallets(
            $db,
            m::mock([
                'readCurrencies' => [10 => 'USD', 20 => 'USD'],
                'convert' => 20000
            ])
        ))->transfer(10, 20, 20000, 'own');
    }

    public function testThrowsWhenOneOfBothWalletsAreNotFound()
    {
        $this->expectException(TransferPathIsNotFound::class);

        (new Wallets(
            m::mock(),
            m::mock([
                'readCurrencies' => [],
            ])
        ))->transfer(10, 20, 20000, 'own');
    }
}
