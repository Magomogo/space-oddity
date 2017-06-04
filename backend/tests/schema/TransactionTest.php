<?php

namespace Acme\Pay\Schema;

use Acme\Pay\Test\Data;
use Acme\Pay\Test\SchemaTestCase;

class TransactionTest extends SchemaTestCase
{
    protected static $schemaId = 'http://acmepay.local/schema/transaction.json#';

    public function testValidTransaction()
    {
        $transaction = new \stdClass();
        $transaction->id = 8888;
        $transaction->timestamp = '2017-06-04 12:48:45';
        $transaction->wallet = new \stdClass();
        $transaction->wallet->id = 12;
        $transaction->wallet->client = Data::johnDoeFromSanFrancisco(23);
        $transaction->wallet->currency = 'USD';
        $transaction->wallet->balance = 10091;
        $transaction->currency = 'RUB';
        $transaction->amount = 128;
        $transaction->balance_change = -128;

        self::assertValid($transaction);
    }
}
