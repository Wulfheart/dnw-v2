<?php

namespace Dnw\Game\Core\Application\Query\GetGameById;

use Dnw\Game\Core\Application\Query\GetGameById\Dto\GameStateEnum;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;

class GetGameByIdQueryHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
    ) {}

    public function handle(GetGameByIdQuery $query): GetGameByIdQueryResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromId($query->id));
        if ($gameResult->hasErr()) {
            return GetGameByIdQueryResult::err(GetGameByIdQueryResult::E_GAME_NOT_FOUND);
        }

        $game = $gameResult->unwrap();

        return GetGameByIdQueryResult::ok(
            new GetGameByIdQueryResultData(
                $query->id,
                GameStateEnum::fromGameState($game->gameStateMachine->currentState()),
                $game->name,
                $game->variant->id->toId(),
                null
                // $game->phasesInfo->currentPhase->mapOr(fn(Phase $phase) => $phase->, null),
            )
        );
    }
}
