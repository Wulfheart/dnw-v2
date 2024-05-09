<?php

namespace Collection;

use ValueObjects\PlayerId;

final class PowerCollection
{
    public static function createFromVariantPowerCollection(
        VariantPowerCollection $variantPowerCollection,
    ): self {
        return new self();
    }

    public function assignRandomly(PlayerId $playerId): void
    {

    }

    public function hasAvailablePowers(): bool
    {
        return false;
    }
}
