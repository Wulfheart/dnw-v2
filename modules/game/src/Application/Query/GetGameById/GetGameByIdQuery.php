<?php

namespace Dnw\Game\Application\Query\GetGameById;

use Dnw\Foundation\Bus\Interface\Query;
use Dnw\Foundation\Identity\Id;

/**
 * @implements Query<GetGameByIdQueryResult>
 */
class GetGameByIdQuery implements Query
{
    public function __construct(
        public Id $id,
        public Id $actor,
    ) {}
}
