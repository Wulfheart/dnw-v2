<?php

namespace Dnw\User\Application\Query\GetUsersByIds;

use Dnw\Foundation\Collection\ArrayCollection;
use Wulfheart\Option\Result;

/**
 * @extends Result<ArrayCollection<UserData>, self::E_*>
 */
final class GetUsersByIdsQueryResult extends Result
{
    public const string E_USER_NOT_FOUND = 'E_USER_NOT_FOUND';
}
