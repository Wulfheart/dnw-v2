<?php

namespace Dnw\Legacy\Transform\ResultData;

final class Power
{
    public function __construct(
        public string $name,
        /** @var array<string> $orders */
        public array $orders
    ) {}
}
