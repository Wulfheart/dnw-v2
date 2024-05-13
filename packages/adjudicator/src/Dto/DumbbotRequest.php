<?php

namespace Dnw\Adjudicator\Dto;

class DumbbotRequest implements AdjudicatorDataInterface
{
    public function __construct(
        public string $current_state_encoded,
        public string $power,
    ) {

    }

    public static function fromArray(array $array): AdjudicatorDataInterface
    {
        return new self(
            $array['current_state_encoded'],
            $array['power'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'current_state_encoded' => $this->current_state_encoded,
            'power' => $this->power,
        ];
    }
}
