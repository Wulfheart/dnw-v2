<?php

namespace Dnw\Game\Application\Command\SubmitOrders;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Identity\Id;

/**
 * @codeCoverageIgnore
 *
 * @implements Command<SubmitOrdersCommandResult>
 */
class SubmitOrdersCommand implements Command
{
    public function __construct(
        public Id $gameId,
        public Id $playerId,
        public bool $markedAsReady,
        /** @var array<string> $orders */
        public array $orders,
    ) {}
}
