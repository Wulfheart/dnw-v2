<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming;

use Dnw\Game\Core\Domain\Game\ValueObject\WeekdayEnum;
use InvalidArgumentException;

readonly class NoAdjudicationWeekdayCollection
{
    /** @var array<int> */
    private array $weekdays;

    /**
     * @param  array<int>  $weekdays
     */
    public function __construct(
        array $weekdays
    ) {
        $weekdays = array_unique($weekdays);
        foreach ($weekdays as $weekday) {
            if ($weekday < 0 || $weekday > 6) {
                throw new InvalidArgumentException('Invalid weekday');
            }
        }

        if (count($weekdays) > 6) {
            throw new InvalidArgumentException('Excluded all days from adjudication');
        }

        $this->weekdays = $weekdays;
    }

    /**
     * @param  array<int>  $weekdays
     */
    public static function fromWeekdaysArray(array $weekdays): self
    {
        return new self($weekdays);
    }

    public function adjudicatesOnWeekday(WeekdayEnum $weekday): bool
    {
        return ! in_array($weekday->value, $this->weekdays);
    }
}
