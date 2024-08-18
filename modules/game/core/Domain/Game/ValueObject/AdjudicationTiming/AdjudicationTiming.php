<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Core\Domain\Game\ValueObject\WeekdayEnum;

class AdjudicationTiming
{
    public function __construct(
        public PhaseLength $phaseLength,
        public NoAdjudicationWeekdayCollection $noAdjudicationWeekdays,
    ) {}

    public function calculateNextAdjudication(DateTime $currentTime): DateTime
    {
        $nextAdjudication = $currentTime->addMinutes($this->phaseLength->minutes());
        while (! $this->noAdjudicationWeekdays->adjudicatesOnWeekday(
            WeekdayEnum::fromCarbon($nextAdjudication)
        )) {
            $nextAdjudication = $nextAdjudication->addDay();
        }

        return $nextAdjudication;
    }
}
