<?php

namespace Dnw\Adjudicator\Dto;

use Spatie\DataTransferObject\Attributes\Strict;

#[Strict]
class VariantDto extends BaseDto
{
    public int $default_end_of_game;

    public string $name;

    /** @var string[] */
    public array $powers;
}
