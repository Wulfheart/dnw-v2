<?php

namespace Dnw\Game\Application\Query\Shared\Game\GameInfo;

use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum as DomainPhaseType;

enum PhaseTypeEnum: string
{
    case MOVEMENT = 'movement';
    case RETREAT = 'retreat';
    case ADJUSTMENT = 'adjustment';
    case NON_PLAYING = 'non_playing';

    public static function fromDomain(DomainPhaseType $phaseTypeEnum): self
    {
        return match ($phaseTypeEnum) {
            DomainPhaseType::MOVEMENT => self::MOVEMENT,
            DomainPhaseType::RETREAT => self::RETREAT,
            DomainPhaseType::ADJUSTMENT => self::ADJUSTMENT,
            DomainPhaseType::NON_PLAYING => self::NON_PLAYING,
        };
    }
}
