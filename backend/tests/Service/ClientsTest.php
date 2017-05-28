<?php

namespace Acme\Pay\Service;

use Mockery as m;
use Acme\Pay\Test;

class ClientsTest extends \PHPUnit_Framework_TestCase
{
    public function testDoesInsert()
    {
        $db = m::mock(['lastInsertId' => 42]);
        $db->shouldReceive('insert')->once();

        (new Clients($db))->create(Test\Data::johnDoeFromSanFrancisco());

    }
}
