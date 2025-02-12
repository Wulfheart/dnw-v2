<?php

namespace Dnw\Game\Application\Query\GetNewGames;

interface GetNewGamesQueryHandlerInterface
{
    public function handle(GetNewGamesQuery $query): GetNewGamesQueryResult;
}
