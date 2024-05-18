<?php

namespace Dnw\Game\Tests\Factory;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Std\Option;

class PhaseFactory
{
    public static function build(
        ?PhaseId $phaseId = null,
        ?PhaseTypeEnum $type = null,
        ?DateTime $adjudicationTime = null
    ): Phase {
        return new Phase(
            $phaseId ?? PhaseId::new(),
            $type ?? PhaseTypeEnum::MOVEMENT,
            Option::fromNullable($adjudicationTime)
        );
    }
}
