<?php

namespace Dnw\Game\Application\Command\CreateVariant;

use Wulfheart\Option\Result;

/**
 * @extends Result<void, CreateVariantCommandResult::E_*>
 */
final class CreateVariantCommandResult extends Result
{
    public const string E_KEY_ALREADY_EXISTS = 'variant_key_already_exists';
}
