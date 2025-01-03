<?php

namespace Dnw\Game\Application\Query\GetGameIdByName;

use Dnw\Foundation\Bus\Interface\Query;

/**
 * @codeCoverageIgnore
 *
 * @implements Query<GetGameIdByNameQueryResult>
 */
class GetGameIdByNameQuery implements Query
{
    public function __construct(
        public string $name,
    ) {}
}
