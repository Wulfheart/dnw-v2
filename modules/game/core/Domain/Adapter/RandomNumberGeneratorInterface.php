<?php

namespace Dnw\Game\Core\Domain\Adapter;

interface RandomNumberGeneratorInterface
{
    public function generate(int $lower, int $upper): int;
}
