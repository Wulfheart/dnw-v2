<?php

namespace Dnw\Adjudicator\Dto;

use Spatie\DataTransferObject\Attributes\Strict;

#[Strict]
class OrderDto extends BaseDto
{
    public string $power;

    /** @var string[] */
    public array $instructions;
}
