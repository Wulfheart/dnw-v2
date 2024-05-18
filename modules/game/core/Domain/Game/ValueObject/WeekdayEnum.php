<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject;

use Dnw\Foundation\DateTime\DateTime;

enum WeekdayEnum: int
{
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 0;

    public static function fromCarbon(DateTime $carbonImmutable): self
    {
        return self::from($carbonImmutable->weekday());
    }
}
