<?php

namespace Dnw\Game\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;

/**
 * @extends Collection<VariantPowerKey>
 */
class VariantPowerIdCollection extends Collection
{
    public function containsVariantPowerId(VariantPowerKey $variantPowerId): bool
    {
        return $this->contains(fn (VariantPowerKey $id) => $id === $variantPowerId);
    }
}
