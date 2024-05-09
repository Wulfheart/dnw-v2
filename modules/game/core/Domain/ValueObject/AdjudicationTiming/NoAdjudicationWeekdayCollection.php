<?php

namespace ValueObjects\AdjudicationTiming;

use ValueObjects\WeekdayEnum;

final readonly class NoAdjudicationWeekdayCollection
{
    public function __construct(
        /** @var array<int> */
        private array $weekdays
    ) {
        foreach ($weekdays as $weekday) {
            if ($weekday < 0 || $weekday > 6) {
                throw new \InvalidArgumentException('Invalid weekday');
            }
        }
    }

    public function adjudicatesOnWeekday(WeekdayEnum $weekday): bool
    {
        return in_array($weekday->value, $this->weekdays);
    }
}
