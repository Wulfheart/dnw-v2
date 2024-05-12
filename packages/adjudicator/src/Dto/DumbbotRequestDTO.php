<?php

namespace Dnw\Adjudicator\Dto;

use Spatie\DataTransferObject\Attributes\Strict;

#[Strict]
class DumbbotRequestDto extends BaseDto
{
    public string $current_state_encoded;

    public string $power;
}
