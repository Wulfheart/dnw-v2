<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Domain\Game\ValueObject\GameStartTiming\JoinLength;

/**
 * @codeCoverageIgnore
 */
class GameStartTimingFactory
{
    public static function build(
        ?DateTime $startOfJoinPhase = null,
        ?JoinLength $joinLength = null,
        ?bool $startWhenReady = null
    ): GameStartTiming {
        return new GameStartTiming(
            $startOfJoinPhase ?? DateTime::now(),
            $joinLength ?? JoinLength::fromDays(2),
            $startWhenReady ?? true
        );
    }
}
