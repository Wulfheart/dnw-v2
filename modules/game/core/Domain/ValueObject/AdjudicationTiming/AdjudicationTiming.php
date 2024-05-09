<?php

namespace Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming;

class AdjudicationTiming
{
    public function __construct(
        public PhaseLength $phaseLength,
        public NoAdjudicationWeekdayCollection $noAdjudicationWeekdays,
    ) {
    }
}
