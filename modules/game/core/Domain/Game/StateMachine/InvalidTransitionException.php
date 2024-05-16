<?php

namespace Dnw\Game\Core\Domain\Game\StateMachine;

class InvalidTransitionException extends \Exception {
    public function __construct(string $from, string $to) {
        parent::__construct("Invalid transition from $from to $to");
    }
}
