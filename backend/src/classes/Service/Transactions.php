<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception\InsufficientBalance;
use Acme\Pay\Exception\TransferPathIsNotFound;
use Doctrine\DBAL;
use Acme\Pay\Types;

class Transactions
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
     * @param integer $ownWalletId
     * @param integer $theirWalletId
     * @param integer $amount
     * @param string $whichCurrency own|their
     * @throws \Exception
     */
    public function create($ownWalletId, $theirWalletId, $amount, $whichCurrency)
    {
        $walletIdToCurrencyCodeMap = $this->currenciesService->readCurrencies($ownWalletId, $theirWalletId);

        if (count($walletIdToCurrencyCodeMap) < 2) {
            throw new TransferPathIsNotFound('One of, or both wallets are not exists');
        }

        $this->db->transactional(function () use ($ownWalletId, $theirWalletId, $amount, $whichCurrency, $walletIdToCurrencyCodeMap) {

            $transactionCurrency = $walletIdToCurrencyCodeMap[
            $whichCurrency === 'own' ? $ownWalletId : $theirWalletId
            ];

            $this->db->insert('transaction', ['currency' => $transactionCurrency, 'amount' => $amount]);
            $transactionId = $this->db->lastInsertId('transaction_id_seq');

            if ($whichCurrency === 'own') {
                $ownAmount = $amount;
                $theirAmount = $this->currenciesService->convert(
                    $amount,
                    $walletIdToCurrencyCodeMap[$ownWalletId],
                    $walletIdToCurrencyCodeMap[$theirWalletId]
                );
            } else {
                $ownAmount = $this->currenciesService->convert(
                    $amount,
                    $walletIdToCurrencyCodeMap[$theirWalletId],
                    $walletIdToCurrencyCodeMap[$ownWalletId]
                );
                $theirAmount = $amount;
            }

            $this->db->insert(
                'transfer',
                [
                    'transaction_id' => $transactionId,
                    'wallet_id' => $ownWalletId,
                    'amount' => -$ownAmount
                ]
            );

            $this->db->insert(
                'transfer',
                [
                    'transaction_id' => $transactionId,
                    'wallet_id' => $theirWalletId,
                    'amount' => $theirAmount
                ]
            );

            try {

                $this->db->executeUpdate(
                    'UPDATE wallet SET balance = balance - :ownAmount WHERE id = :id',
                    [
                        'ownAmount' => $ownAmount,
                        'id' => $ownWalletId,
                    ]
                );

            } catch (DBAL\Exception\DriverException $e) {
                if (strpos($e->getMessage(), 'positive_balance') !== false) {
                    throw new InsufficientBalance();
                }
                throw $e;
            }

            $this->db->executeUpdate(
                'UPDATE wallet SET balance = balance + :theirAmount WHERE id = :id',
                [
                    'theirAmount' => $theirAmount,
                    'id' => $theirWalletId,
                ]
            );
        });
    }

    /**
     * @param \stdClass $client http://acmepay.local/schema/client.json
     * @param array $filters
     * @return array
     */
    public function sortedList($client, $filters)
    {
        $andFilterWhere = $this->filterWhereClause($filters);

        $list = $this->db->fetchAll(<<<SQL
SELECT
  t.id as transaction_id,
  t.timestamp as transaction_timestamp,
  t.currency as transaction_currency,
  t.amount as transaction_amount,
  transfer.amount as transfer_amount,
  w.id as wallet_id,
  w.currency as wallet_currency,
  w.balance as wallet_balance
FROM wallet w
  INNER JOIN transfer ON (transfer.wallet_id = w.id)
  INNER JOIN transaction t ON (transfer.transaction_id = t.id)
  INNER JOIN client c ON (c.id = w.client_id)
WHERE
  c.id = :clientId $andFilterWhere
ORDER BY t.timestamp DESC 
SQL
            ,
            [
                'clientId' => $client->id,
            ]
        );

        return array_map(function ($row) use ($client) {
            return Types\transaction($row, $client);
        }, $list);
    }

    /**
     * @param \stdClass $client http://acmepay.local/schema/client.json
     * @param array $filters
     * @return array
     */
    public function summary($client, $filters)
    {
        $andFilterWhere = $this->filterWhereClause($filters);

        list($sum, $ownCurrency) = $this->db->fetchArray(<<<SQL
SELECT sum(transfer.amount) as sum, w.currency, 1 as sort
FROM
  wallet w
  INNER JOIN transfer ON (transfer.wallet_id = w.id)
  INNER JOIN transaction t ON (transfer.transaction_id = t.id)
WHERE
  w.client_id = :clientId $andFilterWhere
GROUP BY w.client_id, w.currency

UNION

SELECT '0' as sum, w.currency, 2 as sort
FROM wallet w
WHERE
  w.client_id = :clientId

ORDER BY sort
SQL
            ,
            ['clientId' => $client->id]
        );

        return [
            ['currency' => 'USD', 'sum' => $this->currenciesService->convert($sum, $ownCurrency, 'USD')],
            ['currency' => $ownCurrency, 'sum' => $sum]
        ];
    }

    private function filterWhereClause($filter)
    {
        $clause = [];

        if (isset($filter['startDate'])) {
            $clause[] = "(t.timestamp >= to_timestamp('" . $filter['startDate'] . " 00:00:00', 'YYYY-MM-DD HH24:MI:SS'))";
        }

        if (isset($filter['endDate'])) {
            $clause[] = "(t.timestamp <= to_timestamp('" . $filter['endDate'] . " 23:59:59', 'YYYY-MM-DD HH24:MI:SS'))";
        }

        return count($clause) ? ' AND ' . implode(' AND ', $clause) : '';
    }
}
