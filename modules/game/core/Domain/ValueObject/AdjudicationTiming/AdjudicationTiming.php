<?php

namespace ValueObjects\AdjudicationTiming;

final class AdjudicationTiming
{
    public function __construct(
        public PhaseLength $phaseLength,
        public NoAdjudicationWeekdayCollection $noAdjudicationWeekdays,
    ) {
    }
}
