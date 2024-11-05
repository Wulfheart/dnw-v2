<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

use Dnw\Foundation\Identity\Id;

class VariantPowerDto
{
    public function __construct(
        public Id $variantPowerId,
        public string $name,
    ) {}
}
