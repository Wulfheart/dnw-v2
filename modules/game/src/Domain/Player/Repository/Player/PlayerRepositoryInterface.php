<?php

namespace Dnw\Game\Domain\Player\Repository\Player;

use Dnw\Game\Domain\Player\Player;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;

interface PlayerRepositoryInterface
{
    public function load(PlayerId $playerId): Player;
}
