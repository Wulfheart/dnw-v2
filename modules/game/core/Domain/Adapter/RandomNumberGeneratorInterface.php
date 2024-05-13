<?php

namespace Dnw\Game\Core\Domain\Adapter;

interface RandomNumberGeneratorInterface
{
    public function generate(int $min, int $max): int;
}
