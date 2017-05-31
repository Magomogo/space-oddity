<?php

namespace Acme\Pay\Service;

use Acme\Pay\Exception\InsufficientBalance;
use Acme\Pay\Exception\TransferPathIsNotFound;
use Acme\Pay\Wallet\DbMapper;
use Doctrine\DBAL;

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
        $wallet = new \stdClass();
        $wallet->client = $client;
        $wallet->currency = $currency;
        $wallet->balance = $balance;

        $this->db->insert('wallet', (new DbMapper($wallet))->walletTableRow());
        $wallet->id = (int)$this->db->lastInsertId('wallet_id_seq');

        return $wallet;
    }

    /**
     * @param integer $ownWalletId
     * @param integer $theirWalletId
     * @param integer $amount
     * @param string $whichCurrency own|their
     * @throws \Exception
     */
    public function transfer($ownWalletId, $theirWalletId, $amount, $whichCurrency)
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
}
