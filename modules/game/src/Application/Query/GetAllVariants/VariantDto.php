<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

class VariantDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        /** @var array<VariantPowerDto> $powers */
        public array $powers,
    ) {}
}
