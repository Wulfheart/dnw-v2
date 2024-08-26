<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming;

use Dnw\Game\Core\Domain\Game\ValueObject\WeekdayEnum;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoAdjudicationWeekdayCollection::class)]
class NoAdjudicationWeekdayCollectionTest extends TestCase
{
    public function test_cannot_add_weekday_numbers_lower_than_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        NoAdjudicationWeekdayCollection::fromWeekdaysArray([-1]);
    }

    public function test_cannot_add_weekday_numbers_greater_than_6(): void
    {
        $this->expectException(InvalidArgumentException::class);
        NoAdjudicationWeekdayCollection::fromWeekdaysArray([7]);
    }

    public function test_cannot_add_more_than_six_weekdays(): void
    {
        $this->expectException(InvalidArgumentException::class);
        NoAdjudicationWeekdayCollection::fromWeekdaysArray([1, 1, 1, 2, 3, 4, 5, 6, 0]);
    }

    public function test_adjudicatesOnWeekday(): void
    {

        $collection = NoAdjudicationWeekdayCollection::fromWeekdaysArray([1, 1, 1, 2, 3, 4, 5, 6]);
        $this->assertFalse($collection->adjudicatesOnWeekday(WeekdayEnum::MONDAY));
        $this->assertTrue($collection->adjudicatesOnWeekday(WeekdayEnum::SUNDAY));
    }
}
