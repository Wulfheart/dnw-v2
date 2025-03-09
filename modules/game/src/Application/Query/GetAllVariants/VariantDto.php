<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

class VariantDto
{
    public function __construct(
        public string $key,
        public string $name,
        public string $description,
        /** @var array<VariantPowerDto> $powers */
        public array $powers,
    ) {}
}
