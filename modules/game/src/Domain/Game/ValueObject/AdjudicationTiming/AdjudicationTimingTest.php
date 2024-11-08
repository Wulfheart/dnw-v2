<?php

namespace Dnw\Game\Domain\Game\ValueObject\AdjudicationTiming;

use Carbon\CarbonInterface;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Domain\Game\ValueObject\WeekdayEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AdjudicationTiming::class)]
class AdjudicationTimingTest extends TestCase
{
    public function test_calculateNextAdjudication_skips_non_adjudicating_days(): void
    {
        $phaseLength = PhaseLength::fromMinutes(
            CarbonInterface::HOURS_PER_DAY * CarbonInterface::MINUTES_PER_HOUR
        );
        $currentTime = new DateTime('2021-01-01 00:00:00');
        $noAdjudicationWeekdays = NoAdjudicationWeekdayCollection::fromWeekdaysArray([WeekdayEnum::SATURDAY->value, WeekdayEnum::SUNDAY->value]);

        $adjudicationTiming = new AdjudicationTiming($phaseLength, $noAdjudicationWeekdays);

        $nextAdjudication = $adjudicationTiming->calculateNextAdjudication($currentTime);

        $this->assertEquals(
            new DateTime('2021-01-04 00:00:00'),
            $nextAdjudication
        );
    }

    public function test_calculateNextAdjudication_adds_minutes(): void
    {
        $phaseLength = PhaseLength::fromMinutes(10);
        $currentTime = new DateTime('2021-01-01 00:00:00');
        $noAdjudicationWeekdays = NoAdjudicationWeekdayCollection::fromWeekdaysArray([]);
        $adjudicationTiming = new AdjudicationTiming($phaseLength, $noAdjudicationWeekdays);

        $nextAdjudication = $adjudicationTiming->calculateNextAdjudication($currentTime);

        $this->assertEquals(
            new DateTime('2021-01-01 00:10:00'),
            $nextAdjudication
        );
    }
}
