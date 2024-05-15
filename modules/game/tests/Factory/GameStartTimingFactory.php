<?php

namespace Dnw\Game\Tests\Factory;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\JoinLength;

class GameStartTimingFactory
{
    public static function build(
        ?CarbonImmutable $startOfJoinPhase = null,
        ?JoinLength $joinLength = null,
        ?bool $startWhenReady = null
    ): GameStartTiming {
        return new GameStartTiming(
            $startOfJoinPhase ?? CarbonImmutable::now(),
            $joinLength ?? JoinLength::fromDays(2),
            $startWhenReady ?? true
        );
    }
}
