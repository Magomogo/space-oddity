<?php

namespace Acme\Pay\Service;

use Doctrine\DBAL;
use Acme\Pay\Wallet\DbMapper;
use Acme\Pay\Types;
use Mockery\Matcher\Type;

class Wallets
{
    /**
     * @var DBAL\Connection
     */
    private $db;

    /**
     * @var Currencies
     */
    private $currenciesService;

    /**
     * @param DBAL\Connection $db
     * @param Currencies
     */
    public function __construct($db, $currenciesService)
    {
        $this->db = $db;
        $this->currenciesService = $currenciesService;
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
        $wallet = Types\wallet([
            'id' => null,
            'currency' => $currency,
            'balance' => $balance,
        ], $client);

        $this->db->insert('wallet', (new DbMapper($wallet))->walletTableRow());
        $wallet->id = (int)$this->db->lastInsertId('wallet_id_seq');

        return $wallet;
    }

}
