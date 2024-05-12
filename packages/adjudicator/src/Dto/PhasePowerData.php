<?php

namespace Dnw\Adjudicator\Dto;

class PhasePowerData extends Base
{
    public function __construct(
        public int $home_center_count,
        public string $power,
        public int $supply_center_count,
        public int $unit_count,
    ) {

    }
}
