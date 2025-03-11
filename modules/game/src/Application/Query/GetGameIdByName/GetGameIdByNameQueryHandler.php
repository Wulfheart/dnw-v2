<?php

namespace Dnw\Game\Application\Query\GetGameIdByName;

use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\ValueObject\Game\GameName;

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

        return GetGameIdByNameQueryResult::ok($gameId->unwrap()->toId());
    }
}
