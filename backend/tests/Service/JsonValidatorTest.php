<?php

namespace Acme\Pay\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsValidData()
    {
        $currency = (new JsonValidator())->assertValid('EUR', 'http://acmepay.local/schema/currency.json');

        $this->assertSame('EUR', $currency);
    }

    public function testThrowsHTTPExceptionWhenPassedDataIsInvalid()
    {
        $this->expectException(BadRequestHttpException::class);
        (new JsonValidator())->assertValid(json_decode('{"something":"invalid"}'), 'http://acmepay.local/schema/client.json');
    }
}
