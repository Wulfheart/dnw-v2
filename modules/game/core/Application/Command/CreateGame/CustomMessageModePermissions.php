<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

class CustomMessageModePermissions
{
    public function __construct(
        public string $description,
        public bool $message_mode_allow_creation_of_group_chats,
        public bool $message_mode_allow_adjustment_messages,
        public bool $message_mode_allow_move_messages,
        public bool $message_mode_allow_retreat_messages,
        public bool $message_mode_allow_pre_game_messages,
        public bool $message_mode_allow_post_game_messages,
    ) {

    }
}
