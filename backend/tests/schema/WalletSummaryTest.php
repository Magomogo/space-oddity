<?php

namespace Acme\Pay\Schema;

use Acme\Pay\Test\SchemaTestCase;

class WalletSummaryTest extends SchemaTestCase
{
    protected static $schemaId = 'http://acmepay.local/schema/wallet-summary.json#';

    public function testValidTransaction()
    {
        self::assertValid(json_decode(<<<JSON
[
    {
        "currency": "USD",
        "sum": -200
    },
    {
        "currency": "USD",
        "sum": -200
    }
]
JSON
        ));
    }
}
