<?php

namespace Dnw\Game\Core\Domain\ValueObject;

class Count
{
    public static function zero(): self
    {
        return new self(0);
    }

    public function __construct(
        private int $value,
    ) {
    }

    public function int(): int
    {
        return $this->value;
    }
}
