<?php

namespace Dnw\Game\Helper;

use Carbon\CarbonImmutable;
use Dnw\Foundation\DateTime\DateTime;

class PhaseLengthFormatter
{
    public function __construct(
        private string $locale,
    ) {}

    public function formatMinutes(int $minutes): string
    {
        /** @var CarbonImmutable $baseDate */
        $baseDate = CarbonImmutable::create(2021, 1, 1, 0, 0, 0);
        $date = $baseDate->addMinutes($minutes);
        $date->locale($this->locale);

        return $date->diffAsCarbonInterval($baseDate)->forHumans();
    }

    public function formatDateTime(DateTime $dateTime): string
    {
        $dt = $dateTime->toCarbonImmutable();
        $dt->locale($this->locale);

        return $dt->isoFormat('ddd DD. MMM Y HH:mm');
    }
}
