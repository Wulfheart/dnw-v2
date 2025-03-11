<?php

namespace Dnw\Legacy\Transform\ResultData;

final readonly class Turn
{
    public function __construct(
        public int $index,
        /** @var array<Power> $powers */
        public array $powers
    ) {}
}
