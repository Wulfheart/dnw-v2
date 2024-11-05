<?php

namespace App\Web\Game\GamePanel;

use App\Web\Game\ViewModel\GameInformationViewModel;
use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Query\GetGameById\Dto\GameStateEnum;
use Dnw\Game\Application\Query\GetGameById\Dto\VariantPowerDataDto;
use Dnw\Game\Application\Query\GetGameById\GetGameByIdQuery;
use Dnw\Game\Application\Query\GetGameById\GetGameByIdQueryResult;
use Dnw\Game\Helper\PhaseLengthFormatter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GamePanelController
{
    public function __construct(
        private BusInterface $bus,
        private PhaseLengthFormatter $phaseLengthFormatter
    ) {}

    public function show(Request $request, string $id): Application|Response|ResponseFactory
    {
        /** @var GetGameByIdQueryResult $result */
        $result = $this->bus->handle(
            new GetGameByIdQuery(Id::fromString(strtoupper($id)))
        );
        if ($result->hasErr()) {
            return abort(404);
        }
        $data = $result->unwrap();

        $currentlyJoined = $data->variantPowerData->filter(fn (VariantPowerDataDto $variantPowerDataDto) => $variantPowerDataDto->playerId->isSome())->count();
        $totalPlayerCount = $data->variantPowerData->count();
        $gameInfoViewModel = new GameInformationViewModel(
            $data->name,
            null,
            'Pre-game',
            $data->variantName,
            'foo',
            "$currentlyJoined/$totalPlayerCount Spieler sind bisher beigetreten",
            $this->phaseLengthFormatter->formatMinutes($data->phaseLengthInMinutes),
            'Phase',
            'Start',
            (string) $data->nextPhaseStart->toUnixTime(),
            $this->phaseLengthFormatter->formatDateTime($data->nextPhaseStart)
        );
        if ($data->state === GameStateEnum::CREATED) {
            $viewModel = new GamePanelCreatedViewModel(
                $gameInfoViewModel,
                'Refresh'
            );

            return response()->view('game::game.panel.created', ['vm' => $viewModel]);
        }
        if ($data->state === GameStateEnum::PLAYERS_JOINING) {
            /** @var array<Id> $userIds */
            $userIds = $data->variantPowerData
                ->filter(fn (VariantPowerDataDto $power) => $power->playerId->isSome())
                ->map(fn (VariantPowerDataDto $power) => $power->playerId->unwrap())
                ->toArray();

            $viewModel = new GamePanelPlayersJoiningViewModel(
                $gameInfoViewModel,
                $currentlyJoined,
                $totalPlayerCount,
            );

            return response()->view('game::game.panel.players_joining', ['vm' => $viewModel]);
        }

        return response();
    }
}
