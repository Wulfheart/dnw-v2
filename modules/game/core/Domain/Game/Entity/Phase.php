<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Std\Option;

class Phase
{
    public function __construct(
        public PhaseId $phaseId,
        public PhaseTypeEnum $phaseType,
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
