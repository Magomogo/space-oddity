<?php

namespace Acme\Pay\Schema;

use Acme\Pay\Test\SchemaTestCase;
use Acme\Pay\Test;
use Acme\Pay\Types;

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

    public function testClientTypeFunctionGivesValidData()
    {
        self::assertValid(Types\client([
            'id' => 88,
            'name' => 'Maxim',
            'city' => 'Novosibirsk',
            'country' => 'Russia' ,
        ]));
    }
}
