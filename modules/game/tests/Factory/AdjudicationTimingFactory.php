<?php

namespace Dnw\Game\Tests\Factory;

use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\NoAdjudicationWeekdayCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\PhaseLength;

class AdjudicationTimingFactory
{
    /**
     * @param  array<int<1, 7>>|null  $noAdjudicationWeekdays
     */
    public static function build(
        ?PhaseLength $phaseLength = null,
        ?array $noAdjudicationWeekdays = null
    ): AdjudicationTiming {
        return new AdjudicationTiming(
            $phaseLength ?? PhaseLength::fromMinutes(240),
            NoAdjudicationWeekdayCollection::fromWeekdaysArray($noAdjudicationWeekdays ?? [1])
        );
    }
}
