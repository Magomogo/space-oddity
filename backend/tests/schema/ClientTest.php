<?php

namespace Acme\Pay\Schema;

use Acme\Pay\Test\SchemaTestCase;
use Acme\Pay\Test;

class ClientTest extends SchemaTestCase
{
    protected static $schemaId = 'http://acmepay.local/schema/client.json#';

    public function testRegularClientIsValid()
    {
        self::assertValid(Test\Data::johnDoeFromSanFrancisco());
    }

    public function testExtraParamsAreNotAllowed()
    {
        $client = json_decode(<<<JSON
{
    "name": "Jon Doe",
    "country": "Angola",
    "city": "Luanda",
    "job": "Plumber"
}
JSON
        );

        self::assertNotValid($client);
    }

    public function testOnlyNameIsMandatory()
    {
        $client = json_decode(<<<JSON
{
    "name": "Jon Doe"
}
JSON
        );

        self::assertValid($client);
    }

}
