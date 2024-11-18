<?php

namespace Dnw\User\Application\Query\GetUsersByIds;

use Dnw\Foundation\Identity\Id;

final class GetUsersByIdsQuery
{
    public function __construct(
        /** @var list<Id> $ids */
        public array $ids
    ) {}
}
