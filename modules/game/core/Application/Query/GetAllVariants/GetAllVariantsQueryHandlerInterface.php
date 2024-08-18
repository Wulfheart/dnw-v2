<?php

namespace Dnw\Game\Core\Application\Query\GetAllVariants;

interface GetAllVariantsQueryHandlerInterface
{
    /**
     * @return array<VariantDto>
     */
    public function handle(GetAllVariantsQuery $query): array;
}
