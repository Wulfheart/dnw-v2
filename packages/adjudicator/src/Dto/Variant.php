<?php

namespace Dnw\Adjudicator\Dto;

class Variant extends Base
{
    public int $default_end_of_game;

    public string $name;

    /** @var string[] */
    public array $powers;
}
