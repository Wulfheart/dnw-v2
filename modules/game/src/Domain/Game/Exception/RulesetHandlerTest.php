<?php

namespace Dnw\Game\Domain\Game\Exception;

use Dnw\Foundation\Exception\DomainException;
use Dnw\Foundation\Rule\Rule;
use Dnw\Foundation\Rule\Ruleset;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RulesetHandler::class)]
class RulesetHandlerTest extends TestCase
{
    public function test_throws_domain_exception_if_ruleset_fails(): void
    {
        $ruleset = new Ruleset(
            new Rule('fail', true)
        );
        $this->expectException(DomainException::class);
        RulesetHandler::throwConditionally('foo', $ruleset);
    }
}
