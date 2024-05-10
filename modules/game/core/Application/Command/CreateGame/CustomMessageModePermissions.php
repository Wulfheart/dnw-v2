<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

class CustomMessageModePermissions
{
    public function __construct(
        public string $description,
        public bool $messageModeAllowCreationOfGroupChats,
        public bool $messageModeAllowAdjustmentMessages,
        public bool $messageModeAllowMoveMessages,
        public bool $messageModeAllowRetreatMessages,
        public bool $messageModeAllowPreGameMessages,
        public bool $messageModeAllowPostGameMessages,
    ) {

    }
}
