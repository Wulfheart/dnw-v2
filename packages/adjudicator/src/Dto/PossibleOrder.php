<?php

namespace Dnw\Adjudicator\Dto;

class PossibleOrder extends Base
{
    public function __construct(
        public string $power,
        /** @var array<Unit> */
        public array $units,
    ) {

    }
}
