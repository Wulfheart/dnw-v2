<?php

namespace Dnw\Game\Application\Query\GetNewGames;

use Dnw\Foundation\Collection\ArrayCollection;

final readonly class GetNewGamesQueryResult
{
    public function __construct(
        /** @var ArrayCollection<NewGameInfo> $games */
        public ArrayCollection $games,
        public int $totalCount,
    ) {}
}
