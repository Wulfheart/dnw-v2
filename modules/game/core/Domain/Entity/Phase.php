<?php

namespace Dnw\Game\Core\Domain\Entity;

use DateTimeImmutable;
use Dnw\Game\Core\Domain\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseTypeEnum;

class Phase
{
    public function __construct(
        public PhaseId $phaseId,
        public PhaseTypeEnum $phaseType,
        public PhasePowerCollection $orders,
        public DateTimeImmutable $adjudicationTime,
    ) {

    }
}
