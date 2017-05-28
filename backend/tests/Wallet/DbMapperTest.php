<?php

namespace Acme\Pay\Wallet;

use Acme\Pay\Test;

class DbMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMapsWalletDataTypeToATableRow()
    {
        $wallet = new \stdClass();
        $wallet->client = Test\Data::johnDoeFromSanFrancisco();
        $wallet->client->id = 42;
        $wallet->currency = 'USD';
        $wallet->balance = 100;

        $this->assertEquals(
            [
                'client_id' => 42,
                'currency' => 'USD',
                'balance' => 100
            ],
            (new DbMapper($wallet))->walletTableRow()
        );
    }
}
