<?php

namespace Dnw\Game\Core\Domain\ValueObject;

use InvalidArgumentException;

class Count
{
    public static function zero(): self
    {
        return new self(0);
    }

    public static function fromInt(int $i): self
    {
        return new self($i);
    }

    public function __construct(
        private int $value,
    ) {
        if ($this->value < 0) {
            throw new InvalidArgumentException('Count cannot be negative');
        }
    }

    public function int(): int
    {
        return $this->value;
    }
}
