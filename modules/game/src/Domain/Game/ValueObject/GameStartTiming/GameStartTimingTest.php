<?php

namespace Dnw\Game\Domain\Game\ValueObject\GameStartTiming;

use Dnw\Foundation\DateTime\DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GameStartTiming::class)]
class GameStartTimingTest extends TestCase
{
    public function test_join_length_exceeded(): void
    {
        $startOfJoinPhase = new DateTime('2021-01-01');
        $timing = new GameStartTiming($startOfJoinPhase, JoinLength::fromDays(2), false);
        $this->assertTrue($timing->joinLengthExceeded(new DateTime('2021-01-04')));
        $this->assertFalse($timing->joinLengthExceeded(new DateTime('2021-01-03')));
    }
}
