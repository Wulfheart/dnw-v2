<?php

namespace Dnw\Game\Domain\Game\StateMachine;

use Exception;

/**
 * @codeCoverageIgnore
 */
class InvalidTransitionException extends Exception
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Invalid transition from $from to $to");
    }
}
