<?php

namespace Acme\Pay\Service;

use Doctrine\DBAL;

class Currencies
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
     * @param string $currency http://acmepay.local/schema/currency.json
     * @param float $rate
     * @param string $date YYYY-MM-DD
     */
    public function defineRate($currency, $rate, $date)
    {
        $this->db->insert(
            'currency_rates',
            [
                'date' => $date,
                'code' => $currency,
                'rate' =>  $rate
            ]
        );
    }
}
