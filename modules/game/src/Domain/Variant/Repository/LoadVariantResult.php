<?php

namespace Dnw\Game\Domain\Variant\Repository;

use Dnw\Game\Domain\Variant\Variant;
use Wulfheart\Option\Result;

/**
 * @extends Result<Variant, LoadVariantResult::E_*>
 */
class LoadVariantResult extends Result
{
    public const string E_VARIANT_NOT_FOUND = 'variant_not_found';
}
