<?php

namespace Acme\Pay\Client;

class DbMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMapAClientDataTypeIntoDatabaseCells()
    {
        $this->assertEquals(
            [
                'name' => 'John Doe',
                'city' => 'San Francisco',
                'country' => 'USA'
            ],
            (new DbMapper(json_decode(<<<JSON
{
    "name": "John Doe",
    "city": "San Francisco",
    "country": "USA"
}
JSON
            )))->clientsTableRow()
        );
    }
}
