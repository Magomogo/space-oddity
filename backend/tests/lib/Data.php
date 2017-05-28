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
}
