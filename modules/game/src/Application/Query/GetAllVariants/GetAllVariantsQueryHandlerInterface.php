<?php

namespace Dnw\Game\Application\Query\GetAllVariants;

interface GetAllVariantsQueryHandlerInterface
{
    public function handle(GetAllVariantsQuery $query): GetAllVariantsQueryResult;
}
