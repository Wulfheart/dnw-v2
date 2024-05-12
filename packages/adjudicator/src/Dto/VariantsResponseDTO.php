<?php

namespace Dnw\Adjudicator\Dto;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\Casters\ArrayCaster;

#[Strict]
class VariantsResponseDto extends BaseDto
{
    /** @var \Dnw\Adjudicator\Dto\VariantDto[] $variants */
    #[CastWith(ArrayCaster::class, itemType: VariantDto::class)]
    public Collection $variants;
}
