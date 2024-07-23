<?php

namespace Dnw\Game\Core\Application\Query\GetGame;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Application\Query\GetGame\Dto\GameStateEnum;

class GetGameQueryResult
{
    public function __construct(
        public Id $id,
        public GameStateEnum $state,
        public string $name,
        public Id $variantId,
        public ?string $currentPhase,
    ) {

    }
}
