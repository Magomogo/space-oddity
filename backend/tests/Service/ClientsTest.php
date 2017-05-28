<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception\ClientAlreadyExists;
use Doctrine\DBAL\Driver\DriverException;
use Mockery as m;
use Acme\Pay\Test;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class ClientsTest extends \PHPUnit_Framework_TestCase
{
    public function testDoesInsert()
    {
        $db = m::mock(['lastInsertId' => 42]);
        $db->shouldReceive('insert')->once();

        (new Clients($db))->create(Test\Data::johnDoeFromSanFrancisco());

    }

    public function testThrowsWhenClientNameIsTaken()
    {
        $db = m::mock(['lastInsertId' => 42]);
        $db->shouldReceive('insert')->andThrow(new UniqueConstraintViolationException('arrgh', m::mock(DriverException::class)));

        $this->expectException(ClientAlreadyExists::class);

        (new Clients($db))->create(Test\Data::johnDoeFromSanFrancisco());
    }
}
