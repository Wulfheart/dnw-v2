<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Game\Core\Domain\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerId;

class PowerCollection
{
    public static function createFromVariantPowerCollection(
        VariantPowerCollection $variantPowerCollection,
    ): self {
        return new self();
    }

    public function assignRandomly(PlayerId $playerId): void
    {

    }

    public function assign(PlayerId $playerId, VariantPowerId $variantPowerId): void
    {

    }

    public function hasAvailablePowers(): bool
    {
        return false;
    }

    public function unassign(PlayerId $playerId): void
    {

    }

    public function containsPlayer(PlayerId $playerId): bool
    {
        return false;
    }

    public function doesNotContainPlayer(PlayerId $playerId): bool
    {
        return false;
    }

    public function hasPowerFilled(VariantPowerId $variantPowerId): bool
    {
        return false;
    }

    public function hasNoAssignedPowers(): bool
    {
        return false;
    }

    public function getPowerIdByPlayerId(PlayerId $playerId): PowerId
    {
    }
}