<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

use Dnw\Foundation\Identity\Id;

class VariantDto
{
    public function __construct(
        public Id $id,
        public string $name,
        public string $description,
        /** @var array<VariantPowerDto> $powers */
        public array $powers,
    ) {}
}
