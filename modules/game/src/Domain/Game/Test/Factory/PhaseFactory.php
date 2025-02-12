<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Domain\Game\Entity\Phase;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseName;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Wulfheart\Option\Option;

/**
 * @codeCoverageIgnore
 */
class PhaseFactory
{
    public static function build(
        ?PhaseId $phaseId = null,
        ?PhaseTypeEnum $type = null,
        ?PhaseName $name = null,
        ?DateTime $adjudicationTime = null
    ): Phase {
        return new Phase(
            $phaseId ?? PhaseId::new(),
            $type ?? PhaseTypeEnum::MOVEMENT,
            $name ?? PhaseName::fromString('Spring 1901'),
            Option::fromNullable($adjudicationTime)
        );
    }
}
