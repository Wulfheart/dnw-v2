<?php

namespace Dnw\Game\Domain\Game\ValueObject\Phase;

use InvalidArgumentException;
use Stringable;

final readonly class PhaseName implements Stringable
{
    private function __construct(
        private string $s
    ) {
        if (empty(trim($s))) {
            throw new InvalidArgumentException('Phase Name must not be empty');
        }

        if (strlen($s) > 50) {
            throw new InvalidArgumentException('Phase Name must be at most 50 characters');
        }
    }

    public static function fromString(string $s): self
    {
        return new self($s);
    }

    public function __toString(): string
    {
        return $this->s;
    }
}
