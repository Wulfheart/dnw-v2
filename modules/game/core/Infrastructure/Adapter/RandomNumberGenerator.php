<?php

namespace Dnw\Game\Core\Infrastructure\Adapter;

use Dnw\Game\Core\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;

class RandomNumberGenerator implements RandomNumberGeneratorInterface
{
    public function generate(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
