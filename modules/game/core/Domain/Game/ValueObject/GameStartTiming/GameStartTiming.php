<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming;

use Dnw\Foundation\DateTime\DateTime;

class GameStartTiming
{
    public function __construct(
        public DateTime $startOfJoinPhase,
        public JoinLength $joinLength,
        public bool $startWhenReady,
    ) {}

    public function joinLengthExceeded(DateTime $currentTime): bool
    {
        $endDateTime = $this->startOfJoinPhase->addDays($this->joinLength->toDays());

        return $currentTime->greaterThan($endDateTime);
    }
}
