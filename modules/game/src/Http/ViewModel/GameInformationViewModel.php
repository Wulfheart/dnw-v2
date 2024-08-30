<?php

namespace Dnw\Game\Http\ViewModel;

use Dnw\Game\Core\Application\Query\GetGameById\GetGameByIdQueryResultData;

class GameInformationViewModel {
    public function __construct(
        public string $name,
        public ?string $currentPhase,
        public string $currentPhaseType,
        public string $variant,
        public string $variantLink,
        public string $additionalInformation,
        public string $phaseLength,
        public string $phaseLabel,
        public string $nextText,
        public string $nextAsUnixTime,
        public string $nextAsDateTime
    )
    {
    }
}
