<?php

namespace Acme\Pay\Asserts;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @param string $string
 * @return string|null date in YYYY-MM-DD
 */
function assertValidDateOrNull($string)
{
    if (strlen($string)) {

        try {
            $date = (new \DateTime($string))->format('Y-m-d');
        } catch (\Exception $e) {
            throw new BadRequestHttpException(json_encode(['message' => 'Invalid date']));
        }

        return $date;
    }

    return null;
}
