<?php

namespace Dnw\Adjudicator\Dto;

use JsonSerializable;

abstract class Base implements JsonSerializable
{
    public ?string $json;

    /**
     * @param  array<mixed>  $array
     */
    abstract public function fromArray(array $array): static;
}