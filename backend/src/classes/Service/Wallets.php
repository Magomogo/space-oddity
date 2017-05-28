<?php

namespace Acme\Pay\Service;

use Doctrine\DBAL;

class Wallets
{
    /**
     * @var DBAL\Connection
     */
    private $db;

    /**
     * @param DBAL\Connection $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param \stdClass $client http://acmepay.local/schema/client.json
     * @param string $currency http://acmepay.local/schema/currency.json
     * @param integer $balance starting balance in cents
     *
     * @return \stdClass $client http://acmepay.local/schema/wallet.json
     */
    public function create($client, $currency, $balance)
    {
        return new \stdClass();
    }
}
