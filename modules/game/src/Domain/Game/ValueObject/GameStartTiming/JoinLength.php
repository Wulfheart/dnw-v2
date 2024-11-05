<?php

namespace Dnw\Game\Domain\Game\ValueObject\GameStartTiming;

use InvalidArgumentException;

readonly class JoinLength
{
    private function __construct(
        private int $days
    ) {
        if ($days < 1) {
            throw new InvalidArgumentException('Days must be greater than 1');
        }

        if ($days > 365) {
            throw new InvalidArgumentException('Days must be less than 365');
        }
    }

    public static function fromDays(int $days): self
    {
        return new self($days);
    }

    public function toDays(): int
    {
        return $this->days;
    }
}
