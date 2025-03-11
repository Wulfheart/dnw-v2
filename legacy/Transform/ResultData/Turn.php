<?php

namespace Dnw\Legacy\Transform\ResultData;

use JsonSerializable;

final readonly class Turn implements JsonSerializable
{
    public function __construct(
        public int $index,
        /** @var array<Power> $powers */
        public array $powers
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'index' => $this->index,
            'powers' => $this->powers,
        ];
    }
}
