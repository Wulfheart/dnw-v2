<?php

namespace App\Web\Game\ListNewGames;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Game\Application\Query\GetNewGames\GetNewGamesQuery;

final readonly class ListNewGamesController
{
    private const int GAMES_PER_PAGE = 10;

    public function __construct(
        private BusInterface $bus
    ) {}

    public function show(ListNewGamesRequest $request)
    {
        $page = $request->page;
        $newGames = $this->bus->handle(new GetNewGamesQuery(
            self::GAMES_PER_PAGE,
            ($page - 1) * self::GAMES_PER_PAGE
        ));
    }
}
