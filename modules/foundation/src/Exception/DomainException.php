<?php

namespace Dnw\Foundation\Exception;

use Dnw\Foundation\Rule\Ruleset;
use Exception;

class DomainException extends Exception
{
    public function __construct(
        string $message,
        ?Ruleset $ruleset = null,
    ) {
        if ($ruleset?->fails()) {
            $message .= ' Errors: ' . implode(', ', $ruleset->getErrors());
        }
        parent::__construct($message);
    }
}
