<?php

namespace Dnw\Game\Application\Command\CreateGame;

use Wulfheart\Option\Result;

/**
 * @extends Result<void, CreateGameCommandResult::E_*>
 */
class CreateGameCommandResult extends Result
{
    public const string E_UNABLE_TO_LOAD_VARIANT = 'unable_to_load_variant';

    public const string E_NOT_ALLOWED_TO_CREATE_GAME = 'not_allowed_to_create_game';
}
