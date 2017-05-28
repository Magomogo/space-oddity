<?php

namespace Acme\Pay\Service;

use Mockery as m;

class ClientsTest extends \PHPUnit_Framework_TestCase
{
    public function testDoesInsert()
    {
        $db = m::mock(['lastInsertId' => 42]);
        $db->shouldReceive('insert')->once();

        (new Clients($db))->create(json_decode(<<<JSON
{
    "name": "John Doe",
    "city": "San Francisco",
    "country": "USA"
}
JSON
        ));

    }
}
