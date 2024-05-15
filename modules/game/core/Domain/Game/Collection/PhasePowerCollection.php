<?php

namespace Dnw\Game\Core\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;

/**
 * @extends Collection<PhasePowerData>
 */
class PhasePowerCollection extends Collection
{
    public function getByPowerId(PowerId $powerId): PhasePowerData
    {
        return $this->findBy(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->powerId === $powerId
        )->get();
    }
}
