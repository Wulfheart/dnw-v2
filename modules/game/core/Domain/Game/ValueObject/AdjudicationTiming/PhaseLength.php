<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming;

use InvalidArgumentException;

class PhaseLength
{
    private function __construct(
        private int $minutes
    ) {
        if ($minutes < 10) {
            throw new InvalidArgumentException('Length must be greater than 10');
        }
        if ($minutes > 1440) {
            throw new InvalidArgumentException('Length must be less than 1440');
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
