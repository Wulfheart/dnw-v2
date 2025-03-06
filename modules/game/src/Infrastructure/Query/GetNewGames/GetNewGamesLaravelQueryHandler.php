<?php

namespace Dnw\Game\Infrastructure\Query\GetNewGames;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Query\GetNewGames\GetNewGamesQuery;
use Dnw\Game\Application\Query\GetNewGames\GetNewGamesQueryHandlerInterface;
use Dnw\Game\Application\Query\GetNewGames\GetNewGamesQueryResult;
use Dnw\Game\Application\Query\GetNewGames\NewGameInfo;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameInfoDto;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameStateEnum;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\PhaseTypeEnum;
use Dnw\Game\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Infrastructure\Model\Game\GameModel;
use Dnw\Game\Infrastructure\Model\Game\PhaseModel;
use Dnw\Game\Infrastructure\Model\Game\PowerModel;

final readonly class GetNewGamesLaravelQueryHandler implements GetNewGamesQueryHandlerInterface
{
    public function handle(GetNewGamesQuery $query): GetNewGamesQueryResult
    {
        $baseQuery =  GameModel::query()
            ->where('current_state', GameStates::PLAYERS_JOINING);

        $gamesResult = $baseQuery
            ->with([
                'currentPhase',
                'powers',
            ])
            ->offset($query->offset)
            ->limit($query->limit)
            ->get();

        $totalCount = $baseQuery->count();

        /** @var ArrayCollection<NewGameInfo> $games */
        $games = new ArrayCollection();
        foreach ($gamesResult as $game) {

            $players = $game->powers
                ->filter(fn (PowerModel $p) => $p->player_id != null)
                // @phpstan-ignore argument.type
                ->map(fn (PowerModel $powerModel) => Id::fromString($powerModel->player_id))
                ->toArray();

            // We assume that the current phase is set, this is because we query for the state players joining
            /** @var PhaseModel $currentPhase */
            $currentPhase = $game->currentPhase;
            $newGameInfo = new NewGameInfo(
                new GameInfoDto(
                    Id::fromString($game->id),
                    Id::fromString($game->variant_data_variant_id),
                    $game->name,
                    $currentPhase->name,
                    PhaseTypeEnum::fromDomain($currentPhase->type),
                    $game->adjudication_timing_phase_length_in_minutes,
                    GameStateEnum::fromGameState($game->current_state),
                ),
                DateTime::fromCarbon($game->game_start_timing_start_of_join_phase),
                $game->game_start_timing_start_when_ready,
                $game->powers_count,
                ArrayCollection::fromArray($players),
            );
            $games->push($newGameInfo);
        }

        return new GetNewGamesQueryResult(
            $games,
            $totalCount
        );
    }
}
