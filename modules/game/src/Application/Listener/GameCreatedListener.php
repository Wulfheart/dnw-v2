<?php

namespace Dnw\Game\Application\Listener;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Event\Attributes\DomainListener;
use Dnw\Game\Application\Command\InitialGameAdjudication\InitialGameAdjudicationCommand;
use Dnw\Game\Domain\Game\Event\GameCreatedEvent;

#[DomainListener(async: true)]
readonly class GameCreatedListener
{
    public function __construct(
        private BusInterface $bus,
    ) {}

    public function handle(GameCreatedEvent $event): void
    {
        $result = $this->bus->handle(new InitialGameAdjudicationCommand($event->gameId));
        $result->ensure();
    }
}
