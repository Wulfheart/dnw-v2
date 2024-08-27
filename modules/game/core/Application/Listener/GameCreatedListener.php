<?php

namespace Dnw\Game\Core\Application\Listener;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Event\Attributes\DomainListener;
use Dnw\Game\Core\Application\Command\InitialGameAdjudication\InitialGameAdjudicationCommand;
use Dnw\Game\Core\Application\Command\InitialGameAdjudication\InitialGameAdjudicationResult;
use Dnw\Game\Core\Domain\Game\Event\GameCreatedEvent;

#[DomainListener(async: true)]
readonly class GameCreatedListener
{
    public function __construct(
        private BusInterface $bus,
    ) {}

    public function handle(GameCreatedEvent $event): void
    {
        /** @var InitialGameAdjudicationResult $result */
        $result = $this->bus->handle(new InitialGameAdjudicationCommand($event->gameId));
        $result->ensure();
    }
}
