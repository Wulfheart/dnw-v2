<?php

namespace Dnw\Game\Core\Application\Command\SubmitOrders;

use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;

class SubmitOrdersCommandHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private TimeProviderInterface $timeProvider,
    ) {}

    public function handle(SubmitOrdersCommand $command): SubmitOrdersResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromId($command->gameId));
        if ($gameResult->hasErr()) {
            return SubmitOrdersResult::err(SubmitOrdersResult::E_GAME_NOT_FOUND);
        }
        $game = $gameResult->unwrap();

        $orderCollection = OrderCollection::fromStringArray($command->orders);

        $game->submitOrders(
            PlayerId::fromId($command->userId),
            $orderCollection,
            $command->markedAsReady,
            $this->timeProvider->getCurrentTime()
        );

        $this->gameRepository->save($game);

        return SubmitOrdersResult::ok();
    }
}
