<?php

namespace Dnw\Adjudicator\Dto;

class AppliedOrder extends Base
{
    public function __construct(
        /** @var array<string> */
        public array $orders,
        public string $power,
    ) {

    }
}
