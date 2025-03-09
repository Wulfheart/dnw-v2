<?php

namespace Dnw\Game\Domain\Variant\Shared;

use Exception;
use Stringable;

final readonly class VariantPowerId implements Stringable
{
    private function __construct(
        private string $id
    ) {
        if (empty($id)) {
            throw new Exception('VariantId must not be empty');
        }
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
