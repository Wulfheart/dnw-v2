<?php

namespace Dnw\Adjudicator\Dto;

class PhasePowerData implements AdjudicatorDataInterface
{
    public function __construct(
        public int $home_center_count,
        public string $power,
        public int $supply_center_count,
        public int $unit_count,
    ) {

    }

    public static function fromArray(array $array): PhasePowerData
    {
        return new self(
            $array['home_center_count'],
            $array['power'],
            $array['supply_center_count'],
            $array['unit_count'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'home_center_count' => $this->home_center_count,
            'power' => $this->power,
            'supply_center_count' => $this->supply_center_count,
            'unit_count' => $this->unit_count,
        ];
    }
}
