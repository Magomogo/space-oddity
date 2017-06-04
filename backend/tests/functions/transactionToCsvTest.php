<?php

namespace Acme\Pay\Conversions;

use Acme\Pay\Types;
use Acme\Pay\Test\Data;

class transactionToCsvTest extends \PHPUnit_Framework_TestCase
{

    public function testCreatesCsvLine()
    {
        $transaction = self::tr();

        $this->assertSame(<<<CSV
42,2017-06-04 12:16:15.059601+00,USD,100,5676,2,RUB,22,John Doe,USA,San Francisco

CSV
            ,
            transactionToCsv($transaction)
        );

    }

    public function testCanIncludeHeader()
    {
        $transaction = self::tr();

        $this->assertSame(<<<CSV
id,time,currency,amount,balance change,wallet ID,wallet currency,client ID,client name,client country,client city
42,2017-06-04 12:16:15.059601+00,USD,100,5676,2,RUB,22,John Doe,USA,San Francisco

CSV
            ,
            transactionToCsv($transaction, true)
        );

    }

    /**
     * @return \stdClass
     */
    private static function tr()
    {
        return Types\transaction([
            'transaction_id' => 42,
            'transaction_timestamp' => '2017-06-04 12:16:15.059601+00',
            'transaction_currency' => 'USD',
            'transaction_amount' => 100,
            'transfer_amount' => 5676,
            'wallet_id' => 2,
            'wallet_currency' => 'RUB',
            'wallet_balance' => 10000
        ], Data::johnDoeFromSanFrancisco(22));
    }

}
