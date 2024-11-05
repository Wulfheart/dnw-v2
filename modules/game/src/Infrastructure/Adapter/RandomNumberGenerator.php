<?php

namespace Dnw\Game\Infrastructure\Adapter;

use Dnw\Game\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;

class RandomNumberGenerator implements RandomNumberGeneratorInterface
{
    public function generate(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
