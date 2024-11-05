<?php

namespace Dnw\Game\Domain\Game\ValueObject;

use InvalidArgumentException;

class Color
{
    private function __construct(
        private string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Color cannot be empty');
        }
    }

    public static function fromString(string $s): self
    {
        return new self($s);
    }

    public function __toString()
    {
        return $this->value;
    }
}
