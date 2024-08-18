<?php

namespace Dnw\Foundation\Bus;

use League\Tactician\CommandBus;

readonly class TacticianBus implements BusInterface
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(mixed $command): mixed
    {
        return $this->commandBus->handle($command);
    }
}
