<?php

namespace Dnw\Game\Core\Domain\Game\Dto\AdjudicationPowerData;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;

/**
 * @extends Collection<AdjudicationPowerDataDto>
 */
class AdjudicationPowerDataCollection extends Collection
{
    public function getByPowerId(PowerId $powerId): AdjudicationPowerDataDto
    {
        return $this->findBy(
            fn (AdjudicationPowerDataDto $adjudicationPowerDataDto) => $adjudicationPowerDataDto->powerId === $powerId
        )->get();
    }
}
