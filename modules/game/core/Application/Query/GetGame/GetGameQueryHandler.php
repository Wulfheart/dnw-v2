<?php

namespace Dnw\Game\Core\Application\Query\GetGame;

use Dnw\Game\Core\Application\Query\GetGame\Dto\GameStateEnum;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;

class GetGameQueryHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
    ) {}

    public function handle(GetGameQuery $query): GetGameQueryResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromId($query->id));
        if ($gameResult->hasErr()) {
            return GetGameQueryResult::err(GetGameQueryResult::E_GAME_NOT_FOUND);
        }

        $game = $gameResult->unwrap();

        return GetGameQueryResult::ok(
            new GetGameQueryResultData(
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
