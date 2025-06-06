<?php

namespace Dnw\Game\Application\Query\Shared\Game\VariantPowerData;

/**
 * @codeCoverageIgnore
 */
enum VariantPowerStatusEnum: string
{
    case WON = 'won';
    case SURVIVED = 'survived';
    case DRAWN = 'drawn';
    case DEFEATED = 'defeated';
    case NONE = 'none';
}
