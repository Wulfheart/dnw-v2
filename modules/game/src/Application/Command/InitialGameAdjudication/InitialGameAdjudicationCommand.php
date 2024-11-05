<?php

namespace Dnw\Game\Application\Command\InitialGameAdjudication;

use Dnw\Foundation\Identity\Id;

class InitialGameAdjudicationCommand
{
    public function __construct(
        public Id $gameId
    ) {}
}
