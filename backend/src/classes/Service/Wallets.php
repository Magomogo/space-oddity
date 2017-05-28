<?php

namespace Acme\Pay\Service;

use Acme\Pay\Wallet\DbMapper;
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
        $wallet = new \stdClass();
        $wallet->client = $client;
        $wallet->currency = $currency;
        $wallet->balance = $balance;

        $this->db->insert('wallet', (new DbMapper($wallet))->walletTableRow());
        $wallet->id = $this->db->lastInsertId('wallet_id_seq');

        return $wallet;
    }
}
