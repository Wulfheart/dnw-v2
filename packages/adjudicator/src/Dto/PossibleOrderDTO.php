<?php

namespace Dnw\Adjudicator\Dto;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\Casters\ArrayCaster;

#[Strict]
class PossibleOrderDto extends BaseDto
{
    public string $power;

    /** @var \Dnw\Adjudicator\Dto\UnitDto[] $units */
    #[CastWith(ArrayCaster::class, itemType: UnitDto::class)]
    public Collection $units;
}
