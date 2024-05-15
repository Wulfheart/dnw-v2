<?php

namespace Dnw\Game\Core\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;

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
