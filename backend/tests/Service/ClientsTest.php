<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception;
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

        $this->expectException(Exception\ClientAlreadyExists::class);

        (new Clients($db))->create(Test\Data::johnDoeFromSanFrancisco());
    }

    public function testAClientCanBeObtainedByName()
    {
        $db = m::mock();
        $db->shouldReceive('fetchAssoc')
            ->with(m::type('string'), ['John Doe'])
            ->once()
            ->andReturn([
                'id' => 42,
                'name' => 'John Doe',
                'city' => 'San Francisco',
                'country' => 'USA'
            ]);

        $client = (new Clients($db))->getByName('John Doe');

        $this->assertArraySubset(
            (array)Test\Data::johnDoeFromSanFrancisco(),
            (array)$client
        );
    }

    public function testThrowsWhenAClientCannotBeFound()
    {
        $this->expectException(Exception\ClientDoesNotExists::class);

        (new Clients(m::mock(['fetchAssoc' => false])))->getByName('John Doe');
    }
}
