<?php

namespace Dnw\Game\Core\Domain\Entity;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseTypeEnum;
use PhpOption\Option;

class Phase
{
    public function __construct(
        public PhaseId $phaseId,
        public PhaseTypeEnum $phaseType,
        public PhasePowerCollection $orders,
        /** @var Option<CarbonImmutable> $adjudicationTime */
        public Option $adjudicationTime,
    ) {

    }
}
