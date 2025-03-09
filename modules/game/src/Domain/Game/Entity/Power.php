<?php

namespace Dnw\Game\Domain\Game\Entity;

use Dnw\Foundation\Exception\DomainException;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;
use Wulfheart\Option\Option;

class Power
{
    public function __construct(
        public PowerId $powerId,
        /** @var Option<PlayerId> $playerId */
        public Option $playerId,
        public VariantPowerKey $variantPowerId,
        /** @var Option<PhasePowerData> $currentPhaseData */
        public Option $currentPhaseData,
        /** @var Option<OrderCollection> $appliedOrders */
        public Option $appliedOrders,
    ) {}

    public function assign(PlayerId $playerId): void
    {
        if ($this->playerId->isSome()) {
            throw new DomainException(
                "Power $this->powerId already assigned to player $playerId because {$this->playerId->unwrap()} is already assigned"
            );
        }
        $this->playerId = Option::some($playerId);
    }

    public function unassign(): void
    {
        if ($this->playerId->isNone()) {
            throw new DomainException("Power $this->powerId is not assigned to a player");
        }
        $this->playerId = Option::none();
    }

    public function markOrderStatus(bool $orderStatus): void
    {
        if ($this->currentPhaseData->isNone()) {
            throw new DomainException("Power $this->powerId does not have current phase data");
        }
        $this->currentPhaseData->unwrap()->markedAsReady = $orderStatus;
    }

    public function submitOrders(OrderCollection $orderCollection, bool $markedAsReady): void
    {
        if (! $this->ordersNeeded() || $this->ordersMarkedAsReady()) {
            throw new DomainException("Power $this->powerId is ready for adjudication and cannot submit new orders");
        }
        $this->currentPhaseData->unwrap()->orderCollection = Option::some($orderCollection);
        $this->markOrderStatus($markedAsReady);
    }

    public function ordersNeeded(): bool
    {
        return $this->currentPhaseData->mapOr(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->ordersNeeded,
            false
        );
    }

    public function ordersMarkedAsReady(): bool
    {
        return $this->currentPhaseData->mapOr(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->markedAsReady,
            false
        );
    }

    public function readyForAdjudication(): bool
    {
        if (! $this->ordersNeeded()) {
            return true;
        }

        return $this->ordersMarkedAsReady();
    }

    public function proceedToNextPhase(PhasePowerData $newPhaseData, OrderCollection $appliedOrders): void
    {
        $this->currentPhaseData = Option::some($newPhaseData);
        $this->appliedOrders = Option::some($appliedOrders);
    }

    public function persistInitialPhase(PhasePowerData $phasePowerData): void
    {
        $this->currentPhaseData = Option::some($phasePowerData);
    }

    public function isDefeated(): bool
    {
        return $this->currentPhaseData->mapOr(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->supplyCenterCount->int() === 0
                && $phasePowerData->unitCount->int() === 0,
            false
        );
    }
}
