<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use PhpOption\Option;

class Phase
{
    public function __construct(
        public PhaseId $phaseId,
        public PhaseTypeEnum $phaseType,
        /** @var Option<CarbonImmutable> $adjudicationTime */
        public Option $adjudicationTime,
    ) {

    }

    public function adjudicationTimeIsExpired(CarbonImmutable $currentTime): bool
    {
        return $this->adjudicationTime->map(
            fn (CarbonImmutable $adjudicationTime) => $currentTime->gt($adjudicationTime)
        )->getOrElse(false);
    }
}
