<?php

namespace Dnw\Game\Core\Application\Command\JoinGame;

use Dnw\Foundation\Identity\Id;
use Wulfeart\Option\Option;

class JoinGameCommand
{
    public function __construct(
        public Id $gameId,
        public Id $userId,
        /** @var Option<Id> $variantPowerId */
        public Option $variantPowerId,
    ) {}
}
