<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception\InsufficientBalance;
use Acme\Pay\Exception\TransferPathIsNotFound;
use Doctrine\DBAL;

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
     * @param integer $limit
     * @param integer $offset
     * @param array $filters
     * @return array
     */
    public function sortedList($client, $offset, $limit, $filters)
    {
        return [];
    }
}
