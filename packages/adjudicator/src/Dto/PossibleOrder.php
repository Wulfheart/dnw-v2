<?php

namespace Dnw\Adjudicator\Dto;

class PossibleOrder extends Base
{
    public string $power;

    /** @var array<Unit> */
    public Collection $units;
}
