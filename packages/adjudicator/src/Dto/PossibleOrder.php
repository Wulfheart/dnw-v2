<?php

namespace Dnw\Adjudicator\Dto;

class PossibleOrder implements AdjudicatorDataInterface
{
    public function __construct(
        public string $power,
        /** @var array<Unit> */
        public array $units,
    ) {}

    public static function fromArray(array $array): PossibleOrder
    {
        return new self(
            $array['power'],
            array_map(fn ($unit) => Unit::fromArray($unit), $array['units']),
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'power' => $this->power,
            'units' => array_map(fn ($unit) => $unit->jsonSerialize(), $this->units),
        ];
    }
}
