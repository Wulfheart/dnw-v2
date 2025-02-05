<?php

namespace Dnw\Game\Domain\Game\ValueObject;

use Dnw\Foundation\DateTime\DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WeekdayEnum::class)]
class WeekdayEnumTest extends TestCase
{
    public function test_from_carbon(): void
    {
        $this->assertEquals(WeekdayEnum::MONDAY, WeekdayEnum::fromCarbon(new DateTime('2021-08-02')));
    }
}
