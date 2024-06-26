<?php

namespace Dnw\Game\Core\Application\Command\SubmitOrders;

use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;

class SubmitOrdersCommandHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private TimeProviderInterface $timeProvider,
    ) {

    }

    public function handle(SubmitOrdersCommand $command): void
    {
        $game = $this->gameRepository->load(GameId::fromId($command->gameId));

        $orderCollection = OrderCollection::fromStringArray($command->orders);

        $game->submitOrders(
            PlayerId::fromId($command->userId),
            $orderCollection,
            $command->markedAsReady,
            $this->timeProvider->getCurrentTime()
        );

        $this->gameRepository->save($game);
    }
}
