<?php

namespace Dnw\Game\Domain\Variant\ValueObject;

use InvalidArgumentException;

class VariantDescription
{
    private function __construct(
        private string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('VariantApiName cannot be empty');
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
