<?php

namespace Dnw\Game\Application\Command\LeaveGame;

use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Psr\Log\LoggerInterface;

readonly class LeaveGameCommandHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private LoggerInterface $logger,
    ) {}

    public function handle(LeaveGameCommand $command): LeaveGameResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromString($command->gameId));
        if ($gameResult->hasErr()) {
            $this->logger->info('Game not found', ['gameId' => $command->gameId]);

            return LeaveGameResult::err(LeaveGameResult::E_GAME_NOT_FOUND);
        }
        $game = $gameResult->unwrap();

        $game->leave(PlayerId::fromString($command->userId));
        $this->gameRepository->save($game);

        return LeaveGameResult::ok();
    }
}