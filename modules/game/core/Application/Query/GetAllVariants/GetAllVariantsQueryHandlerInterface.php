<?php

namespace Dnw\Game\Core\Application\Query\GetAllVariants;

interface GetAllVariantsQueryHandlerInterface {
    /**
     * @param GetAllVariantsQuery $query
     * @return array<VariantDto>
     */
    public function handle(GetAllVariantsQuery $query): array;
}
