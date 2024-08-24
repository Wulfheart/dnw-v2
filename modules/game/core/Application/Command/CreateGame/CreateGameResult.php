<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

use Std\Result;

/**
 * @extends Result<void, CreateGameResult::E_*>
 */
class CreateGameResult extends Result
{
    public const string E_UNABLE_TO_LOAD_VARIANT = 'unable_to_load_variant';

    public const string E_NOT_ALLOWED_TO_CREATE_GAME = 'not_allowed_to_create_game';
}
