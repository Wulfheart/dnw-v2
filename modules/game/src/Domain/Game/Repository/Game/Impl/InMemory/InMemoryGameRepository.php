<?php

namespace Dnw\Game\Domain\Game\Repository\Game\Impl\InMemory;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Domain\Game\Game;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Game\LoadGameResult;
use Dnw\Game\Domain\Game\Repository\Game\SaveGameResult;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Game\GameName;
use Wulfheart\Option\Option;

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

    public function load(GameId $gameId): LoadGameResult
    {
        if (! isset($this->games[(string) $gameId])) {
            return LoadGameResult::err(LoadGameResult::E_GAME_NOT_FOUND);
        }

        return LoadGameResult::ok($this->games[(string) $gameId]);
    }

    public function save(Game $game): SaveGameResult
    {
        $events = $game->releaseEvents();
        $this->games[(string) $game->gameId] = $game;
        $this->eventDispatcher->dispatchMultiple($events);

        return SaveGameResult::ok();
    }

    public function getGameIdByName(GameName $name): Option
    {
        foreach ($this->games as $game) {
            if ($game->name == $name) {
                return Option::some($game->gameId);
            }
        }

        return Option::none();
    }

    /**
     * @return array<Game>
     */
    public function getAllGames(): array
    {
        return array_values($this->games);
    }
}
