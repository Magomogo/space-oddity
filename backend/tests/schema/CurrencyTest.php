<?php

namespace Acme\Pay\Schema;

use Acme\Pay\Test\SchemaTestCase;

class CurrencyTest extends SchemaTestCase
{
    protected static $schemaId = 'http://acmepay.local/schema/currency.json#';

    /**
     * @param $currency
     * @dataProvider currenciesDataProvider
     */
    public function testValidCurrencies($currency)
    {
        self::assertValid($currency);
    }

    public static function currenciesDataProvider()
    {
        return [
            ['USD'],
            ['EUR'],
            ['RUR'],
        ];
    }

    public function testVietnamDongIsNotAccepted()
    {
        self::assertNotValid('VND');
    }

}
