<?php

namespace Acme\Pay\Conversions;

/**
 * @param \stdClass $transaction http://acmepay.local/schema/transaction.json
 * @param bool $includeHeader
 * @return string
 */
function transactionToCsv($transaction, $includeHeader = false)
{
    $header = [
        'id',
        'time',
        'currency',
        'amount',
        'balance change',
        'wallet ID',
        'wallet currency',
        'client ID',
        'client name',
        'client country',
        'client city',
    ];

    $data = [
        $transaction->id,
        $transaction->timestamp,
        $transaction->currency,
        $transaction->amount,
        $transaction->balance_change,
        $transaction->wallet->id,
        $transaction->wallet->currency,
        $transaction->wallet->client->id,
        $transaction->wallet->client->name,
        $transaction->wallet->client->country,
        $transaction->wallet->client->city,
    ];

    $enquote = function ($cell) {
        $useQuotes = (strpos($cell, "\n") !== false) || (strpos($cell, ',') !== false);

        return implode('', [
            $useQuotes ? '"' : '',
            str_replace('"', '""', $cell),
            $useQuotes ? '"' : ''
        ]);
    };

    $csvLine = function ($data) use ($enquote) {
        return implode(',', array_map($enquote, $data)) . "\n";
    };

    return ($includeHeader ? $csvLine($header) : '' ) . $csvLine($data);
}
