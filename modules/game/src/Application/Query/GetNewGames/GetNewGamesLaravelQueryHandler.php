<?php

namespace Dnw\Game\Application\Query\GetNewGames;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\UserContext\UserContext;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameInfoDto;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameStateEnum;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\PhaseTypeEnum;
use Dnw\Game\Domain\Game\Repository\Game\Impl\Laravel\GameModel;
use Dnw\Game\Domain\Game\Repository\Game\Impl\Laravel\PowerModel;
use Dnw\Game\Domain\Game\Repository\Phase\Impl\Laravel\PhaseModel;
use Dnw\Game\Domain\Game\StateMachine\GameStates;
use Illuminate\Database\Eloquent\Builder;

final readonly class GetNewGamesLaravelQueryHandler implements GetNewGamesQueryHandlerInterface
{
    public function __construct(
        private UserContext $user,
    ) {}

    public function handle(GetNewGamesQuery $query): GetNewGamesQueryResult
    {
        $baseQuery =  GameModel::query()
            ->where('current_state', GameStates::PLAYERS_JOINING);

        $gamesResult = $baseQuery
            ->with([
                'currentPhase',
                'powers',
            ])
            ->when(
                $this->user->getId()->isSome(),
                fn (Builder $query) => $query->whereDoesntHave(
                    'powers',
                    fn (Builder $query) => $query->where('player_id', (string) $this->user->getId()->unwrap())
                )
            )

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
                    $game->variant_data_variant_key,
                    $game->name,
                    $currentPhase->name,
                    PhaseTypeEnum::fromDomain($currentPhase->type),
                    $game->adjudication_timing_phase_length_in_minutes,
                    GameStateEnum::fromGameState($game->current_state),
                ),
                DateTime::fromCarbon($game->game_start_timing_start_of_join_phase),
                $game->game_start_timing_start_when_ready,
                $game->powers->count(),
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
