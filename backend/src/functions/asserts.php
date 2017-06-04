<?php

namespace Acme\Pay\Asserts;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @param string $string
 * @return string date in YYYY-MM-DD
 */
function assertValidDate($string)
{
    try {
        $date = (new \DateTime($string))->format('Y-m-d');
    } catch (\Exception $e) {
        throw new BadRequestHttpException(json_encode(['message' => 'Invalid date']));
    }

    return $date;
}
