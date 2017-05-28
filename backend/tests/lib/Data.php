<?php

namespace Acme\Pay\Test;

abstract class Data
{
    /**
     * @return \stdClass http://acmepay.local/schema/client.json
     */
    public static function johnDoeFromSanFrancisco()
    {
        return json_decode(file_get_contents(__DIR__ . '/../data/john-doe-from-san-francisco.json'));
    }
}
