<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\ValueObject\GameStartTiming;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GameStartTiming::class)]
class GameStartTimingTest extends TestCase
{
    public function test_joinLengthExceeded(): void
    {
        $startOfJoinPhase = new CarbonImmutable('2021-01-01');
        $timing = new GameStartTiming($startOfJoinPhase, JoinLength::fromDays(2), false);
        $this->assertTrue($timing->joinLengthExceeded(new CarbonImmutable('2021-01-04')));
        $this->assertFalse($timing->joinLengthExceeded(new CarbonImmutable('2021-01-03')));
    }
}
