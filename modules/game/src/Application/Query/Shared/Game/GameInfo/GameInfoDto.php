<?php

namespace Dnw\Game\Application\Query\Shared\Game\GameInfo;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Option;

final readonly class GameInfoDto
{
    public function __construct(
        public Id $id,
        public Id $variantId,
        public string $name,
        public string $currentPhaseName,
        public PhaseTypeEnum $currentPhaseType,
        public bool $isAnonymous,
        public GameStateEnum $state,
        public int $currentPhaseLengthInMinutes,
        public DateTime $nextPhaseStart,
        /** @var Option<GameEndInfoDto> */
        public Option $endInfo,
    ) {}
}
