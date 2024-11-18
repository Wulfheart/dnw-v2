<?php

namespace Dnw\User\Application\Query\GetUsersByIds;

interface GetUsersByIdsQueryHandlerInterface
{
    public function handle(GetUsersByIdsQuery $query): GetUsersByIdsQueryResult;
}
