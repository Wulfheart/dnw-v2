<?php

namespace Dnw\Game\Core\Application\Query\GetGame;

use Dnw\Game\Core\Application\Query\GetGame\Dto\GameStateEnum;
use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;

class GetGameQueryHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
    ) {}

    public function handle(GetGameQuery $query): GetGameQueryResult
    {
        $game = $this->gameRepository->load(GameId::fromId($query->id));

        return new GetGameQueryResult(
            $query->id,
            GameStateEnum::fromGameState($game->gameStateMachine->currentState()),
            $game->name,
            $game->variant->id->toId(),
            // $game->phasesInfo->currentPhase->mapOr(fn(Phase $phase) => $phase->, null),
        );
    }
}
