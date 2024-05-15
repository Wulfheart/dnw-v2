<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming;

use InvalidArgumentException;

class PhaseLength
{
    private function __construct(
        private int $minutes
    ) {
        if ($minutes < 10) {
            throw new InvalidArgumentException('Length must be positive');
        }
    }

    public static function fromMinutes(int $length): self
    {
        return new self($length);
    }

    public function minutes(): int
    {
        return $this->minutes;
    }
}
