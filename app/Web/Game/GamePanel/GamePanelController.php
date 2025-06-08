<?php

namespace App\Web\Game\GamePanel;

use App\Foundation\Auth\AuthInterface;
use App\Web\Game\GamePanel\ViewModel\PlayerInfoViewModel;
use App\Web\Game\Helper\PhaseLengthFormatter;
use App\Web\Game\ViewModel\GameInformationViewModel;
use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Query\GetGameById\Dto\VariantPowerDataDto;
use Dnw\Game\Application\Query\GetGameById\GetGameByIdQuery;
use Dnw\Game\Application\Query\GetGameById\GetGameByIdQueryResult;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameStateEnum;
use Dnw\User\Application\Query\GetUsersByIds\GetUsersByIdsQuery;
use Dnw\User\Application\Query\GetUsersByIds\UserData;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class GamePanelController
{
    public function __construct(
        private BusInterface $bus,
        private PhaseLengthFormatter $phaseLengthFormatter,
        private LoggerInterface $logger,
        private AuthInterface $auth
    ) {}

    public function show(Request $request, string $id): Application|Response|ResponseFactory
    {
        /** @var GetGameByIdQueryResult $result */
        $result = $this->bus->handle(
            new GetGameByIdQuery(Id::fromString($id), $this->auth->getUserId())
        );
        if ($result->isErr()) {
            throw new NotFoundHttpException();
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

            return response()->view('game.panel.created', ['vm' => $viewModel]);
        }

        $playerIds = $data->variantPowerData
            ->filter(fn (VariantPowerDataDto $power) => $power->playerId->isSome())
            ->map(fn (VariantPowerDataDto $power) => $power->playerId->unwrap())
            ->toArray();
        $userDataResult = $this->bus->handle(new GetUsersByIdsQuery($playerIds));
        if ($userDataResult->isErr()) {
            $this->logger->error('Failed to get user data', ['error' => $userDataResult->unwrapErr()]);

            throw new NotFoundHttpException();
        }
        /** @var ArrayCollection<UserData> $userData */
        $userData = $userDataResult->unwrap();

        $playerInfoViewModels = $userData->map(fn (UserData $user) => new PlayerInfoViewModel(
            $user->name,
            // TODO
            '#'
        ))->toArray();

        if ($data->state === GameStateEnum::PLAYERS_JOINING) {

            $viewModel = new GamePanelPlayersJoiningViewModel(
                $gameInfoViewModel,
                $currentlyJoined,
                $totalPlayerCount,
                $playerInfoViewModels,
                $data->phases->getCurrentPhase()->unwrap()->linkToAdjudicatedSvg->unwrap(),
                $data->canJoin,
                $data->canLeave
            );

            return response()->view('game.panel.players_joining', ['vm' => $viewModel]);
        }

        return response();
    }
}
