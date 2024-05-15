<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming;

use Carbon\CarbonImmutable;

class GameStartTiming
{
    public function __construct(
        public CarbonImmutable $startOfJoinPhase,
        public JoinLength $joinLength,
        public bool $startWhenReady,
    ) {

    }

    public function joinLengthExceeded(CarbonImmutable $currentTime): bool
    {
        $endDateTime = $this->startOfJoinPhase->addDays($this->joinLength->toDays());

        return $currentTime->greaterThan($endDateTime);
    }
}
