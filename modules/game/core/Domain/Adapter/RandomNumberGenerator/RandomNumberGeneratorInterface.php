<?php

namespace Dnw\Game\Core\Domain\Adapter\RandomNumberGenerator;

interface RandomNumberGeneratorInterface
{
    public function generate(int $min, int $max): int;
}
