<?php

namespace Acme\Pay\Test;

abstract class Data
{
    /**
     * @return \stdClass http://acmepay.local/schema/client.json
     */
    public static function johnDoeFromSanFrancisco($id = null)
    {
        $client = json_decode(file_get_contents(__DIR__ . '/../data/john-doe-from-san-francisco.json'));
        if ($id !== null) {
            $client->id = $id;
        }
        return $client;
    }

    /**
     * @return \stdClass http://acmepay.local/schema/transaction.json
     */
    public static function tenDollarsTransfer()
    {
        $transaction = new \stdClass();
        $transaction->id = 8888;
        $transaction->timestamp = '2017-06-04 12:48:45';
        $transaction->wallet = new \stdClass();
        $transaction->wallet->id = 12;
        $transaction->wallet->client = Data::johnDoeFromSanFrancisco(23);
        $transaction->wallet->currency = 'USD';
        $transaction->wallet->balance = 10091;
        $transaction->currency = 'USD';
        $transaction->amount = 1000;
        $transaction->balance_change = -100;

        return $transaction;
    }
}
