<?php

namespace Dnw\Game\Domain\Adapter\RandomNumberGenerator;

class RandomNumberGenerator implements RandomNumberGeneratorInterface
{
    public function generate(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
