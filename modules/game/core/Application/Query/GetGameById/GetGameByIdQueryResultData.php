<?php

namespace Dnw\Game\Core\Application\Query\GetGameById;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Application\Query\GetGameById\Dto\GameStateEnum;

class GetGameByIdQueryResultData
{
    public function __construct(
        public Id $id,
        public GameStateEnum $state,
        public string $name,
        public Id $variantId,
        public ?string $currentPhase,
    ) {}
}
