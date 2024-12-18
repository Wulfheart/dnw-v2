<?php

namespace Dnw\Game\Application\Command\JoinGame;

use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Option;

class JoinGameCommand
{
    public function __construct(
        public Id $gameId,
        public Id $userId,
        /** @var Option<Id> $variantPowerId */
        public Option $variantPowerId,
    ) {}
}
