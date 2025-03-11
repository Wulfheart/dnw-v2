<?php

namespace Dnw\Legacy\Transform\ResultData;

use JsonSerializable;

final class Power implements JsonSerializable
{
    public function __construct(
        public string $name,
        /** @var array<string> $orders */
        public array $orders
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'orders' => $this->orders,
        ];
    }
}
