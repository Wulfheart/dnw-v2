<?php

namespace Dnw\Adjudicator\Dto;

class Order implements AdjudicatorDataInterface
{
    public function __construct(
        public string $power,
        /** @var array<string> */
        public array $instructions,
    ) {}

    public static function fromArray(array $array): self
    {
        return new self(
            $array['power'],
            $array['instructions'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'power' => $this->power,
            'instructions' => $this->instructions,
        ];
    }
}
