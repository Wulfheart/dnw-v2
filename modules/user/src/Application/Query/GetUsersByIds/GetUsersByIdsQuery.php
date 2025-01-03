<?php

namespace Dnw\User\Application\Query\GetUsersByIds;

use Dnw\Foundation\Bus\Interface\Query;
use Dnw\Foundation\Identity\Id;

/**
 * @implements Query<GetUsersByIdsQueryResult>
 */
final class GetUsersByIdsQuery implements Query
{
    public function __construct(
        /** @var list<Id> $ids */
        public array $ids
    ) {}
}
