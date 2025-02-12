<?php

namespace Dnw\Game\Application\Query\CanParticipateInAnotherGame;

use Dnw\Foundation\Bus\Interface\Query;
use Dnw\Foundation\Identity\Id;

/**
 * @implements Query<CanParticipateInAnotherGameQueryResult>
 */
final class CanParticipateInAnotherGameQuery implements Query
{
    public function __construct(
        public Id $playerId
    ) {}
}
