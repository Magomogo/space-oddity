<?php

namespace Acme\Pay\Client;

use Acme\Pay\Test;

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
            (new DbMapper(Test\Data::johnDoeFromSanFrancisco()))->clientsTableRow()
        );
    }
}
