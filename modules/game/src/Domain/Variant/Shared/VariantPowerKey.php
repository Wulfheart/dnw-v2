<?php

namespace Dnw\Game\Domain\Variant\Shared;

use Exception;
use Stringable;

final readonly class VariantPowerKey implements Stringable
{
    private function __construct(
        private string $key
    ) {
        if (empty($key)) {
            throw new Exception('VariantId must not be empty');
        }
    }

    public static function fromString(string $key): self
    {
        return new self($key);
    }

    public function __toString(): string
    {
        return $this->key;
    }
}
