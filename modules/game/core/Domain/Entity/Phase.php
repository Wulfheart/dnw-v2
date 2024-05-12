<?php

namespace Dnw\Game\Core\Domain\Entity;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\Collection\WinnerCollection;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use PhpOption\Option;

class Phase
{
    public function __construct(
        public PhaseId $phaseId,
        public PhaseTypeEnum $phaseType,
        public PhasePowerCollection $orders,
        /** @var Option<CarbonImmutable> $adjudicationTime */
        public Option $adjudicationTime,
        /** @var Option<WinnerCollection> $winnerCollection */
        public Option $winnerCollection,
    ) {

    }

    public function hasWinners(): bool
    {
        return $this->winnerCollection->isDefined();
    }

    public function needsOrders(PowerId $powerId): bool
    {
        return $this->orders->needsOrders($powerId);
    }
}
