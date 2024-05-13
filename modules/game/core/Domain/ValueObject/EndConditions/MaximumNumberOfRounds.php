<?php

namespace Dnw\Game\Core\Domain\ValueObject\EndConditions;

use InvalidArgumentException;

class MaximumNumberOfRounds
{
    public function __construct(
        private int $rounds
    ) {
        if ($rounds < 4  || $rounds > 200) {
            throw new InvalidArgumentException('Number of rounds must be between 4 and 200, ' . $rounds . ' given.');
        }
    }

    public function rounds(): int
    {
        return $this->rounds;
    }
}
