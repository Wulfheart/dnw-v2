<?php

namespace Dnw\Foundation\UserContext;

use Dnw\Foundation\Identity\Id;
use Exception;
use Wulfheart\Option\Option;

final class PermissionDeniedException extends Exception
{
    /**
     * @param  Option<Id>  $id
     */
    public function __construct(
        Option $id,
        string $permission,
    ) {
        parent::__construct("Permission denied for user with id {$id->mapOr(fn (Id $id) => (string) $id, 'NONE')} to perform action {$permission}");
    }
}
