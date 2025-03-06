<?php

namespace Dnw\Game\Application\Query\GetNewGames;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameInfoDto;

final readonly class NewGameInfo
{
    public function __construct(
        public GameInfoDto $gameInfo,
        public DateTime $gameStartTime,
        public bool $startWhenReady,
        public int $totalPowerCount,
        /** @var ArrayCollection<Id> $players */
        public ArrayCollection $players,
    ) {}
}
