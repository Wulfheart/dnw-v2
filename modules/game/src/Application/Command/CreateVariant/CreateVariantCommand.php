<?php

namespace Dnw\Game\Application\Command\CreateVariant;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Collection\ArrayCollection;

/**
 * @codeCoverageIgnore
 *
 * @implements Command<CreateVariantCommandResult>
 */
final readonly class CreateVariantCommand implements Command
{
    public function __construct(
        public string $key,
        public string $name,
        public string $description,
        public int $defaultSupplyCentersToWinCount,
        public int $totalSupplyCenterCount,
        /** @var ArrayCollection<VariantPowerInfo> $powers */
        public ArrayCollection $powers,
    ) {}
}
