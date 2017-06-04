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
