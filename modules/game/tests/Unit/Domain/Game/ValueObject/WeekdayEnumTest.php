<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\ValueObject;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\ValueObject\WeekdayEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WeekdayEnum::class)]
class WeekdayEnumTest extends TestCase
{
    public function test_fromCarbon(): void
    {
        $this->assertEquals(WeekdayEnum::MONDAY, WeekdayEnum::fromCarbon(new CarbonImmutable('2021-08-02')));
    }
}
