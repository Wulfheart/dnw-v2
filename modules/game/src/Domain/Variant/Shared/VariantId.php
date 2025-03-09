<?php

namespace Dnw\Game\Domain\Variant\Shared;

use Exception;

final readonly class VariantId
{
    private function __construct(
        private string $id
    ) {}

    public static function fromString(string $id): self
    {
        if (empty($id)) {
            throw new Exception('VariantId must not be empty');
        }

        return new self($id);
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function clone(): self
    {
        return new self($this->id);
    }
}
