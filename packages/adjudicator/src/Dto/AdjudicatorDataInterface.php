<?php

namespace Dnw\Adjudicator\Dto;

use JsonSerializable;

interface AdjudicatorDataInterface extends JsonSerializable
{
    /**
     * @param  array<mixed>  $array
     */
    public static function fromArray(array $array): self;
}
