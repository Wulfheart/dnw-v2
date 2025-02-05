<?php

namespace Dnw\Game\Domain\Player\Test;

use Dnw\Game\Domain\Player\Player;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;

/**
 * @codeCoverageIgnore
 */
final class PlayerBuilder
{
    private int $numberOfGames = 0;

    public static function initialize(): self
    {
        return new self();
    }

    public function inTooManyGames(): self
    {
        $this->numberOfGames = 4;

        return $this;
    }

    public function numberOfGamesIsOk(): self
    {
        $this->numberOfGames = 3;

        return $this;
    }

    public function build(): Player
    {
        return new Player(PlayerId::new(), $this->numberOfGames);
    }
}
