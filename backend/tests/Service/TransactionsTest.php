<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception;
use Mockery as m;

class TransactionsTest extends \PHPUnit_Framework_TestCase
{
    public function testDoesTransfer()
    {
        $db = m::mock(['fetchColumn' => 'EUR', 'lastInsertId' => 88]);
        $db->shouldReceive('transactional')->with(m::on(function ($arg) { $arg(); return true; }));
        $db->shouldReceive('insert')->with('transaction', m::any())->ordered()->once();
        $db->shouldReceive('insert')->with('transfer', m::any())->ordered()->twice();
        $db->shouldReceive('executeUpdate')->ordered()->twice();

        (new Transactions(
            $db,
            m::mock([
                'readCurrencies' => [10 => 'USD', 20 => 'USD'],
                'convert' => 20000
            ])
        ))->create(10, 20, 20000, 'own');
    }

    public function testThrowsWhenOneOfBothWalletsAreNotFound()
    {
        $this->expectException(Exception\TransferPathIsNotFound::class);

        (new Transactions(
            m::mock(),
            m::mock([
                'readCurrencies' => [],
            ])
        ))->create(10, 20, 20000, 'own');
    }

    public function testThrowsWhenPositiveBalanceViolation()
    {
        $db = m::mock(['insert' => null, 'lastInsertId' => 88]);
        $db->shouldReceive('transactional')->with(m::on(function ($arg) { $arg(); return true; }));

        $db->shouldReceive('executeUpdate')
            ->andThrow(new \Doctrine\DBAL\Exception\DriverException(
                'Check violation: 7 ERROR:  new row for relation "wallet" violates check constraint "positive_balance"',
                m::mock(\Doctrine\DBAL\Driver\DriverException::class)
            ));

        $this->expectException(Exception\InsufficientBalance::class);

        (new Transactions(
            $db,
            m::mock([
                'readCurrencies' => [10 => 'USD', 20 => 'USD'],
                'convert' => 20000
            ])
        ))->create(10, 20, 20000, 'own');

    }
}
