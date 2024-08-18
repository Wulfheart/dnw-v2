<?php

namespace Dnw\Adjudicator\Dto;

class VariantsResponse implements AdjudicatorDataInterface
{
    public function __construct(
        /** @var array<Variant> */
        public array $variants,
    ) {}

    public static function fromArray(array $array): VariantsResponse
    {
        return new self(
            array_map(
                fn (array $variant) => Variant::fromArray($variant),
                $array['variants']
            ),
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'variants' => array_map(fn ($variant) => $variant->jsonSerialize(), $this->variants),
        ];
    }
}
