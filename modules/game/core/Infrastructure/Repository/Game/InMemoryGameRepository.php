<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Game;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;

class InMemoryGameRepository implements GameRepositoryInterface
{
    /** @var array<string, Game> */
    private array $games = [];

    /**
     * @param  array<Game>  $games
     */
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        array $games = []
    ) {
        foreach ($games as $game) {
            $this->games[(string) $game->gameId] = $game;
        }
    }

    /**
     * @throws NotFoundException
     */
    public function load(GameId $gameId): Game
    {
        return $this->games[(string) $gameId] ?? throw new NotFoundException();
    }

    public function save(Game $game): void
    {
        $events = $game->releaseEvents();
        $this->games[(string) $game->gameId] = $game;
        $this->eventDispatcher->dispatchMultiple($events);
    }

    /**
     * @return array<Game>
     */
    public function getAllGames(): array
    {
        return array_values($this->games);
    }
}
