<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception\CurrencyRateForThisDateIsAlreadyDefined;
use Acme\Pay\Exception\CurrencyRateUndefined;
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

    public function convert($amount, $currency, $targetCurrency)
    {
        $list = $this->db->fetchAll(
            'SELECT code, rate FROM currency_rates WHERE code IN (?) AND date = ?',
            [
                [$currency, $targetCurrency],
                date('Y-m-d')
            ],
            [
                DBAL\Connection::PARAM_STR_ARRAY,
                \PDO::PARAM_STR
            ]
        );

        if (count($list) !== 2) {
            throw new CurrencyRateUndefined(
                sprintf('Rates for conversion %s -> %s are not known', $currency, $targetCurrency)
            );
        }

        $rates = array_combine(
            array_column($list, 'code'),
            array_column($list, 'rate')
        );

        return (int)round(
            $amount / $rates[$currency] * $rates[$targetCurrency]
        );
    }
}
