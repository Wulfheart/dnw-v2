<?php

namespace Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming;

class PhaseLength
{
    public static function fromMinutes(int $length): self
    {
        return new self($length);
    }

    public function minutes(): int
    {
    }
}
