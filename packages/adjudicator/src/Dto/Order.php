<?php

namespace Dnw\Adjudicator\Dto;

class Order extends Base
{
    public string $power;

    /** @var array<string> */
    public array $instructions;
}
