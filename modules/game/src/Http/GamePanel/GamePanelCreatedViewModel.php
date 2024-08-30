<?php

namespace Dnw\Game\Http\GamePanel;

use Dnw\Game\Core\Application\Query\GetGameById\GetGameByIdQueryResultData;
use Dnw\Game\Http\ViewModel\GameInformationViewModel;

/**
 * @codeCoverageIgnore
 */
class GamePanelCreatedViewModel {
    public function __construct(
        public GameInformationViewModel $gameInfo,
        public string $labelRefresh,
    )
    {
    }
}
