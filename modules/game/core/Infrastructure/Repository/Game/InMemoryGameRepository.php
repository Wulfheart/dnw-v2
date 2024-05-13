<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Game;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Core\Domain\Aggregate\Game;
use Dnw\Game\Core\Domain\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\ValueObject\Game\GameId;

class InMemoryGameRepository implements GameRepositoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        /** @var array<string, Game> $games */
        private array $games = []
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function load(GameId $gameId): Game
    {
        if (array_key_exists((string) $gameId, $this->games)) {
            return $this->games[(string) $gameId];
        }
        throw new NotFoundException();
    }

    public function save(Game $game): void
    {
        $events = $game->releaseEvents();
        $this->games[(string) $game->gameId] = $game;
        $this->eventDispatcher->dispatchMultiple($events);
    }
}
