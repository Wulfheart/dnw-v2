<?php

namespace Dnw\Game\Application\Query\GetNewGames;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameInfoDto;

final readonly class GetNewGamesQueryResult
{
    public function __construct(
        /** @var ArrayCollection<GameInfoDto> $games */
        public ArrayCollection $games,
        public int $totalCount,
    ) {}
}
