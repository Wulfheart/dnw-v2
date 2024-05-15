<?php

namespace Dnw\Game\Core\Domain\Game\Exception;

use Dnw\Foundation\Exception\DomainException;
use Dnw\Foundation\Rule\Ruleset;

class RulesetHandler
{
    /**
     * @throws DomainException
     */
    public static function throwConditionally(
        string $message,
        Ruleset $ruleset
    ): void {
        if ($ruleset->fails()) {
            throw new DomainException($message, $ruleset);
        }
    }
}
