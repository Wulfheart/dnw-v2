<?php

namespace Dnw\Game\Core\Application\Query\GetGameIdByName;

use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;

readonly class GetGameIdByNameQueryHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository
    ) {}

    public function handle(GetGameIdByNameQuery $query): GetGameIdByNameQueryResult
    {
        $gameId = $this->gameRepository->getGameIdByName(GameName::fromString($query->name));
        if ($gameId->isNone()) {
            return GetGameIdByNameQueryResult::err(GetGameIdByNameQueryResult::E_GAME_NOT_FOUND);
        }

        return GetGameIdByNameQueryResult::ok((string) $gameId->unwrap());
    }
}
