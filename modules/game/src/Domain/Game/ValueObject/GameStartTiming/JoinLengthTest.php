<?php

namespace Dnw\Game\Domain\Game\ValueObject\GameStartTiming;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JoinLength::class)]
class JoinLengthTest extends TestCase
{
    public function test_at_least_1(): void
    {
        $this->expectException(InvalidArgumentException::class);
        JoinLength::fromDays(0);
    }

    public function test_at_most_365(): void
    {
        $this->expectException(InvalidArgumentException::class);
        JoinLength::fromDays(366);
    }

    public function test_from_days(): void
    {
        $joinLength = JoinLength::fromDays(5);
        $this->assertEquals(5, $joinLength->toDays());
    }
}
