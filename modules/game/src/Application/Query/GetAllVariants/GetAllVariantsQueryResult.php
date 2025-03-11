<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

/**
 * @codeCoverageIgnore
 */
class GetAllVariantsQueryResult
{
    public function __construct(
        /** @var array<VariantDto> $variants */
        public array $variants,
    ) {}
}
