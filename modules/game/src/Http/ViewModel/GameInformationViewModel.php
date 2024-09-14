<?php

namespace Dnw\Game\Http\ViewModel;

class GameInformationViewModel
{
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
    ) {}
}
