<?php

namespace Dnw\Game\Application\Query\Shared\Game\GameInfo;

use Dnw\Foundation\Identity\Id;

final readonly class GameInfoDto
{
    public function __construct(
        public Id $id,
        public Id $variantId,
        public string $name,
        public string $currentPhaseName,
        public PhaseTypeEnum $currentPhaseType,
        public int $phaseLengthInMinutes,
        public GameStateEnum $state,
    ) {}
}
