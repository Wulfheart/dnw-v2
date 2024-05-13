<?php

namespace Dnw\Game\Core\Domain\ValueObject\Game;

use InvalidArgumentException;
use Stringable;

class GameName implements Stringable
{
    private function __construct(
        private string $s
    ) {
        if (strlen($s) < 3 || strlen($s) > 50) {
            throw new InvalidArgumentException('Game name must be between 3 and 50 characters');
        }
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function __toString()
    {
        return $this->s;
    }
}
