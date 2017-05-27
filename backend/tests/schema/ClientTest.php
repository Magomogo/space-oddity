<?php

namespace Acme\Pay\Schema;

use Acme\Pay\Test\SchemaTestCase;

class ClientTest extends SchemaTestCase
{
    protected static $schemaId = 'file://' . __DIR__  . '/../../www/schema/client.json#';

    public function testRegularClientIsValid()
    {
        $client = json_decode(<<<JSON
{
    "name": "Jon Doe",
    "country": "Angola",
    "city": "Luanda"
}
JSON
        );

        self::assertValid($client);
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
