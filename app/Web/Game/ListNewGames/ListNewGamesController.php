<?php

namespace App\Web\Game\ListNewGames;

use App\Web\Game\ViewModel\GameInformationViewModel;
use App\Web\Helper\Pagination;
use Dnw\Foundation\Bus\BusInterface;
use Dnw\Game\Application\Query\GetNewGames\GetNewGamesQuery;
use Dnw\Game\Application\Query\GetNewGames\NewGameInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

final readonly class ListNewGamesController
{
    private const int GAMES_PER_PAGE = 10;

    public function __construct(
        private BusInterface $bus
    ) {}

    public function show(ListNewGamesRequest $request): Response|RedirectResponse
    {
        $page = $request->page;
        $newGames = $this->bus->handle(new GetNewGamesQuery(
            self::GAMES_PER_PAGE,
            ($page - 1) * self::GAMES_PER_PAGE
        ));

        $pagination = new Pagination($page, self::GAMES_PER_PAGE, $newGames->totalCount);

        if ($pagination->isOutOfRange()) {
            return redirect(route('games.list.new', ['page' => $pagination->calculateLastPage()]));
        }

        $newGames->games->map(
            fn (NewGameInfo $i) => new GameInformationViewModel(
                $i->gameInfo->name,
                null,
                'Pre-game',
                $i->gameInfo->variantKey,
                'foo',
            )
        )->toArray();

        return null;

    }
}
