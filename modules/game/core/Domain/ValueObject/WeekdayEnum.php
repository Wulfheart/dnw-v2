<?php

namespace Dnw\Game\Core\Domain\ValueObject;

use Carbon\CarbonImmutable;

enum WeekdayEnum: int
{
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 0;

    public static function fromCarbon(CarbonImmutable $carbonImmutable): self
    {
        return self::from($carbonImmutable->weekday());
    }
}
