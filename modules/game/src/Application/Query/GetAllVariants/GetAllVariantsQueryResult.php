<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

class GetAllVariantsQueryResult
{
    public function __construct(
        /** @var array<VariantDto> $variants */
        public array $variants,
    ) {}
}
