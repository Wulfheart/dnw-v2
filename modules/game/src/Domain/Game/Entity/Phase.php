<?php

namespace Dnw\Game\Domain\Game\Entity;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseName;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Wulfheart\Option\Option;

class Phase
{
    public function __construct(
        public PhaseId $phaseId,
        public PhaseTypeEnum $phaseType,
        public PhaseName $phaseName,
        /** @var Option<DateTime> $adjudicationTime */
        public Option $adjudicationTime,
    ) {}

    public function adjudicationTimeIsExpired(DateTime $currentTime): bool
    {
        return $this->adjudicationTime->mapOr(
            fn (DateTime $adjudicationTime) => $currentTime->greaterThan($adjudicationTime),
            false
        );
    }
}
