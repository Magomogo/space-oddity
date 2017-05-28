<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception\CurrencyRateForThisDateIsAlreadyDefined;
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
     * @throws CurrencyRateForThisDateIsAlreadyDefined
     */
    public function defineRate($currency, $rate, $date)
    {
        try {
            $this->db->insert(
                'currency_rates',
                [
                    'date' => $date,
                    'code' => $currency,
                    'rate' => $rate
                ]
            );
        } catch (DBAL\Exception\UniqueConstraintViolationException $e) {
            throw new CurrencyRateForThisDateIsAlreadyDefined(
                sprintf('Rate for %s on %s is already defined', $currency, $date)
            );
        }
    }
}
