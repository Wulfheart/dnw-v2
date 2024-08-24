<?php

namespace Dnw\Game\Core\Domain\Player\Repository\Player;

use Dnw\Game\Core\Domain\Player\Player;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;

interface PlayerRepositoryInterface
{
    public function load(PlayerId $playerId): Player;
}
