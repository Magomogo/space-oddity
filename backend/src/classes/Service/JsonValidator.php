<?php

namespace Acme\Pay\Service;

use League\JsonGuard\Validator;
use League\JsonReference\Dereferencer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonValidator
{
    public function assertValid($data, $schemaId)
    {
        $validator = new Validator($data, Dereferencer::draft4()->dereference($schemaId));

        if ($validator->fails()) {
            throw new BadRequestHttpException(json_encode([
                'message' => 'Json schema violation',
                'details' => array_map(function ($e) { return $e->toArray(); } , $validator->errors()),
            ]));
        }

        return $data;
    }
}
