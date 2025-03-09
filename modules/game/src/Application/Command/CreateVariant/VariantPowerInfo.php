<?php

namespace Dnw\Game\Application\Command\CreateVariant;

/**
 * @codeCoverageIgnore
 */
final readonly class VariantPowerInfo
{
    public function __construct(
        public string $key,
        public string $name,
        public string $color
    ) {}
}
