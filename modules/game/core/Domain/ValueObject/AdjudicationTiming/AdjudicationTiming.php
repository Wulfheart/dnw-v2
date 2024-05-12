<?php

namespace Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\ValueObject\WeekdayEnum;

class AdjudicationTiming
{
    public function __construct(
        public PhaseLength $phaseLength,
        public NoAdjudicationWeekdayCollection $noAdjudicationWeekdays,
    ) {
    }

    public function calculateNextAdjudication(CarbonImmutable $currentTime): CarbonImmutable
    {
        $nextAdjudication = $currentTime->addMinutes($this->phaseLength->minutes());
        while ($this->noAdjudicationWeekdays->adjudicatesOnWeekday(
            WeekdayEnum::fromCarbon($nextAdjudication)
        )) {
            $nextAdjudication = $nextAdjudication->addDay();
        }

        return $nextAdjudication;
    }
}
