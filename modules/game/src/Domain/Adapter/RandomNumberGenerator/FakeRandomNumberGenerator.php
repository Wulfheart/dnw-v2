<?php

namespace Dnw\Game\Domain\Adapter\RandomNumberGenerator;

class FakeRandomNumberGenerator implements RandomNumberGeneratorInterface
{
    public function __construct(
        private readonly int $number,
    ) {}

    public function generate(int $min, int $max): int
    {
        return $this->number;
    }
}
