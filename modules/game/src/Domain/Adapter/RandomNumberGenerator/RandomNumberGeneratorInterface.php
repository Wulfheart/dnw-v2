<?php

namespace Dnw\Game\Domain\Adapter\RandomNumberGenerator;

interface RandomNumberGeneratorInterface
{
    public function generate(int $min, int $max): int;
}
