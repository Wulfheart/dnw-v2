<?php

namespace Dnw\Adjudicator;

use Exception;

class HttpException extends Exception
{
    public function __construct(
        string $message,
        int $statusCode,
    ) {
        parent::__construct(
            $message . ' Status Code: ' . $statusCode
        );
    }
}
