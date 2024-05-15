<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Variant;

use InvalidArgumentException;
use Stringable;

class VariantApiName implements Stringable
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
