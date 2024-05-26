<?php

namespace Dnw\Game\Core\Application\Query\GetGame;

interface GetGameQueryHandlerInterface
{
    public function handle(GetGameQuery $query): GetGameQueryResult;
}
