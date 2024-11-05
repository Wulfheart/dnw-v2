<?php

namespace Dnw\Game\Domain\Game\Dto;

use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;

class AdjudicationPowerDataDto
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        public PowerId $powerId,
        public NewPhaseData $newPhaseData,
        public OrderCollection $appliedOrders,
    ) {}
}
