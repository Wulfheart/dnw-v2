<?php

namespace Dnw\Game\Application\Query\GetNewGames;

use Dnw\Foundation\Bus\Interface\Query;

/**
 * @codeCoverageIgnore
 *
 * @implements Query<GetNewGamesQueryResult>
 */
final readonly class GetNewGamesQuery implements Query
{
    public function __construct(
        public int $limit,
        public int $offset
    ) {}
}
