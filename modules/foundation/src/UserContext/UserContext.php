<?php

namespace Dnw\Foundation\UserContext;

use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Option;

final class UserContext
{
    public function __construct(
        /** @var Option<Id> $id */
        private Option $id,
        /** @var array<string> $permissions */
        private array $permissions = [],
    ) {}

    /**
     * @return Option<Id>
     */
    public function getId(): Option
    {
        return $this->id;
    }

    public function checkPermission(string $permission): void
    {
        if (! in_array($permission, $this->permissions)) {
            throw new PermissionDeniedException(
                $this->id,
                $permission,
            );
        }
    }
}
