<?php

namespace Dnw\Game\Application\Query\Shared\Game\GameInfo;

use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 */
final readonly class GameInfoDto
{
    public function __construct(
        public Id $id,
        public string $variantKey,
        public string $name,
        public string $currentPhaseName,
        public PhaseTypeEnum $currentPhaseType,
        public int $phaseLengthInMinutes,
        public GameStateEnum $state,
    ) {}
}
