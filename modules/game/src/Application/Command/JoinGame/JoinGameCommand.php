<?php

namespace Dnw\Game\Application\Command\JoinGame;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Option;

/**
 * @implements Command<JoinGameCommandResult>
 */
class JoinGameCommand implements Command
{
    public function __construct(
        public Id $gameId,
        public Id $userId,
        /** @var Option<Id> $variantPowerId */
        public Option $variantPowerId,
    ) {}
}
