<?php

namespace Dnw\Game\Application\Command\CreateGame;

class CustomMessageModePermissions
{
    public function __construct(
        public string $description,
        public bool $allowOnlyPublicMessages,
        public bool $allowCreationOfGroupChats,
        public bool $allowAdjustmentMessages,
        public bool $allowMoveMessages,
        public bool $allowRetreatMessages,
        public bool $allowPreGameMessages,
        public bool $allowPostGameMessages,
    ) {}
}
