<?php

namespace Dnw\Game\Http\GamePanel;

use Dnw\Game\Http\GamePanel\ViewModel\PlayerInfoViewModel;
use Dnw\Game\Http\ViewModel\GameInformationViewModel;

class GamePanelPlayersJoiningViewModel
{
    public function __construct(
        public GameInformationViewModel $gameInfo,
        public int $currentPlayerNumber,
        public int $totalPlayerNumber,
        /** @var array<PlayerInfoViewModel> $players */
        public array $players,
        public string $mapLink,
        public bool $canJoin,
        public bool $canLeave,
    ) {}
}
