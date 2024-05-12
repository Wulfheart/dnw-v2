<?php

namespace Dnw\Adjudicator\Dto;

class VariantsResponse extends Base
{
    public function __construct(
        /** @var array<Variant> */
        public array $variants,
    ) {

    }
}
