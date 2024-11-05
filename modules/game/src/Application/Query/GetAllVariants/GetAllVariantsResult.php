<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

class GetAllVariantsResult
{
    public function __construct(
        /** @var array<VariantDto> $variants */
        public array $variants,
    ) {}
}
