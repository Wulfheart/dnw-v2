<?php

namespace Dnw\Game\Tests\Mother;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use PhpOption\Option;

class PhaseMother
{
    public static function factory(
        ?PhaseId $phaseId = null,
        ?PhaseTypeEnum $type = null,
        ?CarbonImmutable $adjudicationTime = null
    ): Phase {
        return new Phase(
            $phaseId ?? PhaseId::generate(),
            $type ?? PhaseTypeEnum::MOVEMENT,
            //@phpstan-ignore-next-line
            Option::fromValue($adjudicationTime)
        );
    }
}
