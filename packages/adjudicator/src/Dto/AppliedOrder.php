<?php

namespace Dnw\Adjudicator\Dto;

class AppliedOrder implements AdjudicatorDataInterface
{
    public function __construct(
        /** @var array<string> */
        public array $orders,
        public string $power,
    ) {

    }

    public static function fromArray(array $array): AppliedOrder
    {
        return new self(
            $array['orders'],
            $array['power'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'orders' => $this->orders,
            'power' => $this->power,
        ];
    }
}
