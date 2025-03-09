<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

class VariantPowerDto
{
    public function __construct(
        public string $variantPowerKey,
        public string $name,
    ) {}
}
