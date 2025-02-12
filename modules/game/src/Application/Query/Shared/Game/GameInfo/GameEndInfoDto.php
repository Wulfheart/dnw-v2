<?php

namespace Dnw\Game\Application\Query\Shared\Game\GameInfo;

use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Option;

final readonly class GameEndInfoDto
{
    public function __construct(
        public GameEndTypeEnum $type,
        /** @var Option<Id> $winnerId */
        public Option $winnerId,
    ) {}
}
