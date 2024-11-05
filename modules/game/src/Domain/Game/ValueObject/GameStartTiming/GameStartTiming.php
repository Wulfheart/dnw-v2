<?php

namespace Dnw\Game\Domain\Game\ValueObject\GameStartTiming;

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
        return $currentTime->greaterThan($this->endOfJoinPhase());
    }

    public function endOfJoinPhase(): DateTime
    {
        return $this->startOfJoinPhase->addDays($this->joinLength->toDays());
    }
}
