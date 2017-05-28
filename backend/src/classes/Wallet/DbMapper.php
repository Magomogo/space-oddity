<?php

namespace Acme\Pay\Wallet;

class DbMapper
{
    /**
     * @var \stdClass
     */
    private $wallet;

    /**
     * @param \stdClass $wallet http://acmepay.local/schema/wallet.json
     */
    public function __construct($wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * @return array
     */
    public function walletTableRow()
    {
        return [
            'client_id' => $this->wallet->client->id,
            'currency' => $this->wallet->currency,
            'balance' => $this->wallet->balance
        ];
    }
}
