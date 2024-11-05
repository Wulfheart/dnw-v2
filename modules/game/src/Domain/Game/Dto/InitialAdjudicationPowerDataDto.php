<?php

namespace Dnw\Game\Domain\Game\Dto;

use Dnw\Game\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;

class InitialAdjudicationPowerDataDto
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        public PowerId $powerId,
        public NewPhaseData $phasePowerData,
    ) {}
}
