<?php

namespace Dnw\Adjudicator\Dto;

class Variant implements AdjudicatorDataInterface
{
    public function __construct(
        public int $default_end_of_game,
        public string $name,
        /** @var array<string> */
        public array $powers,
    ) {

    }

    public static function fromArray(array $array): Variant
    {
        return new self(
            $array['default_end_of_game'],
            $array['name'],
            $array['powers'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'default_end_of_game' => $this->default_end_of_game,
            'name' => $this->name,
            'powers' => $this->powers,
        ];
    }
}
