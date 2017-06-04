<?php

namespace Acme\Pay\Types;

/**
 * @param $dbRow
 * @return \stdClass http://acmepay.local/schema/client.json
 */
function client($dbRow)
{
    $client = new \stdClass();
    $client->id = (int)$dbRow['id'];
    $client->name = $dbRow['name'];
    ($dbRow['city'] !== null) && $client->city = $dbRow['city'];
    ($dbRow['country'] !== null) && $client->country = $dbRow['country'];

    return $client;
}

function wallet($dbRow, $client, $columnNamesPrefix = '')
{
    $wallet = new \stdClass();
    $wallet->id = $dbRow[$columnNamesPrefix . 'id'];
    $wallet->client = $client;
    $wallet->currency = $dbRow[$columnNamesPrefix . 'currency'];
    $wallet->balance = $dbRow[$columnNamesPrefix . 'balance'];

    return $wallet;
}

function transaction($dbRow, $client)
{
    $t = new \stdClass();
    $t->id = $dbRow['transaction_id'];
    $t->timestamp = $dbRow['transaction_timestamp'];
    $t->currency = $dbRow['transaction_currency'];
    $t->amount = $dbRow['transaction_amount'];
    $t->balance_change = $dbRow['transfer_amount'];
    $t->wallet = wallet($dbRow, $client, 'wallet_');

    return $t;
}
