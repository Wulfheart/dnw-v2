<?php

namespace App\Web\Game\GamePanel;

use App\Web\Game\GamePanel\ViewModel\PlayerInfoViewModel;
use App\Web\Game\ViewModel\GameInformationViewModel;

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
