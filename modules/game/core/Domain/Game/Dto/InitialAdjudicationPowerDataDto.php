<?php

namespace Dnw\Game\Core\Domain\Game\Dto;

use Dnw\Game\Core\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;

class InitialAdjudicationPowerDataDto
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        public PowerId $powerId,
        public NewPhaseData $phasePowerData,
    ) {
    }
}
