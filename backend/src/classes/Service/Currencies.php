<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception\CurrencyRateForThisDateIsAlreadyDefined;
use Acme\Pay\Exception\CurrencyRateUndefined;
use Doctrine\DBAL;
use Stash\Interfaces\ItemInterface;
use Stash\Pool;

class Currencies
{
    /**
     * @var DBAL\Connection
     */
    private $db;

    /**
     * @var Pool
     */
    private $cache;

    /**
     * @param DBAL\Connection $db
     * @param null|Pool $cache
     */
    public function __construct($db, $cache = null)
    {
        $this->db = $db;
        $this->cache = $cache;
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

    /**
     * @param integer $walletId1
     * @param integer $walletId2
     * @return array wallet id to currency code map
     * @throws \InvalidArgumentException
     */
    public function readCurrencies($walletId1, $walletId2)
    {
        $dbFetchFunction = function () use ($walletId1, $walletId2) {
            $list = $this->db->fetchAll(
                'SELECT id, currency FROM wallet WHERE id IN (?)',
                [[$walletId1, $walletId2]],
                [DBAL\Connection::PARAM_INT_ARRAY]
            );

            return array_combine(
                array_column($list, 'id'),
                array_column($list, 'currency')
            );
        };

        return $this->cache ?
            $this->invokeCache($dbFetchFunction, 'acmepay/wallet-currencies/' . $walletId1 . '-' . $walletId2)
            :
            $dbFetchFunction();
    }

    public function convert($amount, $currency, $targetCurrency)
    {
        if ($currency === $targetCurrency) {
            return $amount;
        }

        $ratesFetchFunction = function () use ($currency, $targetCurrency) {

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

            return array_combine(
                array_column($list, 'code'),
                array_column($list, 'rate')
            );

        };

        $rates = $this->cache ?
            $this->invokeCache($ratesFetchFunction, 'acmepay/currency-rates/' . $currency . '-' . $targetCurrency)
            :
            $ratesFetchFunction();

        return (int)round(
            $amount / $rates[$currency] * $rates[$targetCurrency]
        );
    }

    /**
     * This function tries to load an object from the cache using $cacheKey
     *
     * @param callable $expensiveFn
     * @param string $cacheKey
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function invokeCache($expensiveFn, $cacheKey)
    {
        /** @var ItemInterface $cacheItem */
        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheItem->expiresAfter(86400);

        $result = $cacheItem->get();

        if($cacheItem->isHit()) {
            return $result;
        }

        $cacheItem->lock();

        $result = $expensiveFn();

        $this->cache->save($cacheItem->set($result));

        return $result;
    }
}
