<?php

namespace Dnw\Adjudicator\Dto;

use Spatie\DataTransferObject\Attributes\Strict;

#[Strict]
class PhasePowerDataDto extends BaseDto
{
    public int $home_center_count;

    public string $power;

    public int $supply_center_count;

    public int $unit_count;
}
