<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Game\Core\Domain\ValueObject\PlayerId;

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

    public function hasAvailablePowers(): bool
    {
        return false;
    }
}
