<?php

namespace Dnw\Game\Core\Application\Command\SubmitOrders;

use Dnw\Foundation\Identity\Id;

class SubmitOrdersCommand
{
    public function __construct(
        public Id $gameId,
        public Id $userId,
        /** @var array<string> $orders */
        public array $orders,
    ) {
    }
}
