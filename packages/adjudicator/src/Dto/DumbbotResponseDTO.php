<?php

namespace Dnw\Adjudicator\Dto;

use Spatie\DataTransferObject\Attributes\Strict;

#[Strict]
class DumbbotResponseDto extends BaseDto
{
    /** @var string[] */
    public array $orders;

    public string $power;
}
