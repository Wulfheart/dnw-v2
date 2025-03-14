<?php

namespace Dnw\Game\Domain\Variant\ValueObject\VariantPower;

use InvalidArgumentException;
use Stringable;

class VariantPowerName implements Stringable
{
    private function __construct(
        private string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('VariantPowerName cannot be empty');
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
