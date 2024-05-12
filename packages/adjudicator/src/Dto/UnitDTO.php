<?php

namespace Dnw\Adjudicator\Dto;

use Spatie\DataTransferObject\Attributes\Strict;

#[Strict]
class UnitDto extends BaseDto
{
    /** @var string[] */
    public array $possible_orders;

    public string $space;
}
