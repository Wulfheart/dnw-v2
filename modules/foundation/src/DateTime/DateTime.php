<?php

namespace Dnw\Foundation\DateTime;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;

class DateTime
{
    private CarbonImmutable $dateTime;

    public function __construct(?string $dateTime = null)
    {
        $dateTime = $dateTime ?? 'now';
        $this->dateTime = Carbon::parse($dateTime)->milli(0)->toImmutable();
    }

    public static function fromCarbon(Carbon $carbon): self
    {
        return new self($carbon->toDateTimeString());
    }

    public static function now(): self
    {
        return new self('now');
    }

    public function toCarbon(): Carbon
    {
        return new Carbon($this->dateTime);
    }

    public function addDays(int $toDays): self
    {
        return new self($this->dateTime->addDays($toDays)->toDateTimeString());
    }

    public function addDay(): self
    {
        return $this->addDays(1);
    }

    public function addMinutes(int $int): self
    {
        return new self($this->dateTime->addMinutes($int)->toDateTimeString());
    }

    public function addMinute(): self
    {
        return new self($this->dateTime->addMinutes(1)->toDateTimeString());
    }

    public function toDateTimeString(): string
    {
        return $this->dateTime->toDateTimeString();
    }

    public function greaterThan(self $dateTime): bool
    {
        return $this->dateTime->greaterThan($dateTime->dateTime);
    }

    public function weekday(): int
    {
        $weekday = $this->dateTime->weekday();
        if (is_int($weekday)) {
            return $weekday;
        }
        throw new Exception('Weekday is not an integer');
    }
}
