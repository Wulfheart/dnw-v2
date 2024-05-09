<?php

namespace Entity;

use Collection\PhasePowerCollection;
use DateTimeImmutable;
use ValueObjects\Phase\PhaseId;
use ValueObjects\Phase\PhaseTypeEnum;

final class Phase
{
    public function __construct(
        public PhaseId $phaseId,
        public PhaseTypeEnum $phaseType,
        public PhasePowerCollection $orders,
        public DateTimeImmutable $adjudicationTime,
    ) {

    }
}
