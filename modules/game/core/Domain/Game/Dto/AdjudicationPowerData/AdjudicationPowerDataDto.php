<?php

namespace Dnw\Game\Core\Domain\Game\Dto\AdjudicationPowerData;

use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;

class AdjudicationPowerDataDto
{
    public function __construct(
        public PowerId $powerId,
        public PhasePowerData $newPhaseData,
        public OrderCollection $appliedOrders,
    ) {

    }
}
