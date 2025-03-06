<?php

namespace Dnw\Game\Application\Command\SubmitOrders;

use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Psr\Log\LoggerInterface;

class SubmitOrdersCommandHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private TimeProviderInterface $timeProvider,
        private LoggerInterface $logger,
    ) {}

    public function handle(SubmitOrdersCommand $command): SubmitOrdersCommandResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromId($command->gameId));
        if ($gameResult->hasErr()) {
            $this->logger->info('Game not found', ['gameId' => $command->gameId]);

            return SubmitOrdersCommandResult::err(SubmitOrdersCommandResult::E_GAME_NOT_FOUND);
        }
        $game = $gameResult->unwrap();

        $orderCollection = OrderCollection::fromStringArray($command->orders);

        $game->submitOrders(
            PlayerId::fromId($command->playerId),
            $orderCollection,
            $command->markedAsReady,
            $this->timeProvider->getCurrentTime()
        );

        $this->gameRepository->save($game);

        return SubmitOrdersCommandResult::ok();
    }
}
