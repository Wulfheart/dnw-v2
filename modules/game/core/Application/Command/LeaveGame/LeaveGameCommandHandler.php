<?php

namespace Dnw\Game\Core\Application\Command\LeaveGame;

use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;

readonly class LeaveGameCommandHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository
    ) {}

    public function handle(LeaveGameCommand $command): void
    {
        $game = $this->gameRepository->load(GameId::fromString($command->gameId));
        $game->leave(PlayerId::fromString($command->userId));
        $this->gameRepository->save($game);
    }
}
