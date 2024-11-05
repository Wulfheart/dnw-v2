<?php

namespace Dnw\Game\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;

/**
 * @extends Collection<VariantPowerId>
 */
class VariantPowerIdCollection extends Collection
{
    public function containsVariantPowerId(VariantPowerId $variantPowerId): bool
    {
        return $this->contains(fn (VariantPowerId $id) => $id === $variantPowerId);
    }
}
