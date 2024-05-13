<?php

namespace Dnw\Adjudicator\Dto;

class Unit implements AdjudicatorDataInterface
{
    public function __construct(
        /** @var array<string> */
        public array $possible_orders,
        public string $space,
    ) {

    }

    public static function fromArray(array $array): Unit
    {
        return new self(
            $array['possible_orders'],
            $array['space'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'possible_orders' => $this->possible_orders,
            'space' => $this->space,
        ];
    }
}
