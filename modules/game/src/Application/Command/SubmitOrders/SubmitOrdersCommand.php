<?php

namespace Dnw\Game\Application\Command\SubmitOrders;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Identity\Id;

/**
 * @implements Command<SubmitOrdersCommandResult>
 */
class SubmitOrdersCommand implements Command
{
    public function __construct(
        public Id $gameId,
        public Id $userId,
        public bool $markedAsReady,
        /** @var array<string> $orders */
        public array $orders,
    ) {}
}
