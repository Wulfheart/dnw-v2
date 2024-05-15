<?php

namespace Dnw\Game\Core\Domain\Game\Dto;

use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;

class InitialAdjudicationPowerDataDto
{
    public function __construct(
        public PowerId $powerId,
        public PhasePowerData $phasePowerData,
    ) {
    }
}
