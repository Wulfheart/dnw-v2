<?php

namespace Dnw\Adjudicator\Dto;

class DumbbotRequest extends Base
{
    public function __construct(
        public string $current_state_encoded,
        public string $power,
    ) {

    }
}
