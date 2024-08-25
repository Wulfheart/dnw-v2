<?php

namespace Dnw\Game\Core\Application\Query\GetAllVariants;

interface GetAllVariantsQueryHandlerInterface
{
    public function handle(GetAllVariantsQuery $query): GetAllVariantsResult;
}
