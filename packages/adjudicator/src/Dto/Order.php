<?php

namespace Dnw\Adjudicator\Dto;

class Order extends Base
{
    public function __construct(
        public string $power,
        /** @var array<string> */
        public array $instructions,
    ) {

    }
}
