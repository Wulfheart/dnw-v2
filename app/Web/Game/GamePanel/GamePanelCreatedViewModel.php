<?php

namespace App\Web\Game\GamePanel;

use App\Web\Game\ViewModel\GameInformationViewModel;

/**
 * @codeCoverageIgnore
 */
class GamePanelCreatedViewModel
{
    public function __construct(
        public GameInformationViewModel $gameInfo,
        public string $labelRefresh,
    ) {}
}
