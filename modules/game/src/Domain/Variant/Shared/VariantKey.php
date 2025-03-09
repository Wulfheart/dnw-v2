<?php

namespace Dnw\Game\Domain\Variant\Shared;

use Exception;

final readonly class VariantKey
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

    public function clone(): self
    {
        return new self($this->key);
    }
}
