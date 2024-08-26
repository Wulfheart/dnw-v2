<?php

namespace Dnw\Foundation\Bus;

use Exception;

class FakeBus implements BusInterface
{
    /**
     * @var array<array{string|object|callable(mixed): bool, mixed}>
     */
    private array $handledCommands = [];

    /**
     * @param  array{string|object|callable(mixed): bool, mixed}  $commands
     */
    public function __construct(
        array ...$commands,
    ) {
        $this->handledCommands = $commands;
    }

    public function handle(mixed $command): mixed
    {
        foreach ($this->handledCommands as $registeredCommand) {
            [$handledCommand, $result] = $registeredCommand;
            if (is_string($handledCommand) && $command instanceof $handledCommand) {
                return $result;
            }
            if (is_object($handledCommand) && $command == $handledCommand) {
                return $result;
            }
            if (is_callable($handledCommand) && $handledCommand($command)) {
                return $result;
            }
        }
        throw new Exception('Command not found');
    }
}
