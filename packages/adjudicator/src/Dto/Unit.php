<?php

namespace Dnw\Adjudicator\Dto;

class Unit extends Base
{
    public function __construct(
        /** @var array<string> */
        public array $possible_orders,
        public string $space,
    ) {

    }
}
