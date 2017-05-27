<?php

namespace Acme\Pay\Test;

use League\JsonReference\Dereferencer;
use League\JsonGuard\Validator;

abstract class SchemaTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $schemaId;

    private static $schema;

    public static function setUpBeforeClass()
    {
        self::$schema = Dereferencer::draft4()->dereference(static::$schemaId);
    }

    protected static function assertValid($data)
    {
        $validator = new Validator($data, self::$schema);

        if ($validator->fails()) {
            self::fail(print_r(array_map(function ($e) { return $e->toArray(); } , $validator->errors()), true));
        }

        self::assertTrue($validator->passes());
    }

    protected static function assertNotValid($json)
    {
        $validator = new Validator($json, self::$schema);

        if ($validator->passes()) {
            self::fail(print_r($json, true) . ' is valid against the schema');
        }

        self::assertTrue($validator->fails());
    }
}
