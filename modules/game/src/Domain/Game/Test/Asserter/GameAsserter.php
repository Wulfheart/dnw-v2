<?php

namespace Dnw\Game\Domain\Game\Test\Asserter;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Game;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use Technically\CallableReflection\CallableReflection;
use Technically\CallableReflection\Parameters\TypeReflection;

/**
 * @codeCoverageIgnore
 */
readonly class GameAsserter
{
    private function __construct(
        private Game $game
    ) {}

    public static function assertThat(Game $game): self
    {
        return new self($game);
    }

    /**
     * @param  class-string|callable  $event
     * @param  int<0, max>  $expectedCount
     */
    public function hasEvent(string|callable $event, int $expectedCount = 1): self
    {
        $events = ArrayCollection::build(...$this->game->inspectEvents());
        if (is_string($event)) {
            $filteredEvents = $events->filter(
                fn (object $eventObject): bool => $eventObject instanceof $event
            );
            $count = $filteredEvents->count();
            $stringEventTypes = [$event];

        } else {
            $reflection = CallableReflection::fromCallable($event);
            $firstParameter = $reflection->getParameters()[0]
                ?? throw new InvalidArgumentException('The given callable has no parameters.');
            $eventTypes = $firstParameter->getTypes();

            $stringEventTypes = array_map(fn (TypeReflection $type) => $type->getType(), $eventTypes);

            $filteredEvents = $events->filter(
                function (object $eventObject) use ($eventTypes): bool {
                    foreach ($eventTypes as $parameterType) {
                        if ($eventObject::class === $parameterType->getType()) {
                            return true;
                        }
                    }

                    return false;
                }
            );

            $count = 0;
            foreach ($filteredEvents as $potentialEventCandidate) {
                $result = $event($potentialEventCandidate);
                if ($result) {
                    $count++;
                }
            }
        }

        $events = implode(', ', $stringEventTypes);
        Assert::assertTrue(
            $count === $expectedCount,
            "Expected to dispatch events [$events] $expectedCount times, but dispatched $count times."
        );

        return $this;
    }

    public function hasNotEvent(string $eventName): self
    {
        $result = ArrayCollection::build(...$this->game->inspectEvents())->findBy(fn ($event) => $event::class === $eventName);
        Assert::assertTrue($result->isNone(), "Event $eventName found in game events.");

        return $this;
    }

    public function hasState(string $state): self
    {
        Assert::assertEquals($state, $this->game->gameStateMachine->currentState());

        return $this;
    }

    public function hasNotCurrentPhaseId(PhaseId $phaseId): self
    {
        Assert::assertNotEquals($phaseId, $this->game->phasesInfo->currentPhase->unwrap()->phaseId);

        return $this;
    }

    public function hasGameId(GameId $gameId): self
    {
        Assert::assertEquals($gameId, $this->game->gameId);

        return $this;
    }

    public function powerIdHasPlayerId(PowerId $powerId, PlayerId $playerId): self
    {
        $power = $this->game->powerCollection->getByPowerId($powerId);
        Assert::assertEquals($playerId, $power->playerId->unwrap());

        return $this;
    }

    public function hasPlayerInGame(PlayerId $playerId): self
    {
        $exists = $this->game->powerCollection->findByPlayerId($playerId)->isSome();
        Assert::assertTrue($exists, "Player $playerId is not a member of the game.");

        return $this;
    }

    public function hasPlayerNotInGame(PlayerId $playerId): self
    {
        $exists = $this->game->powerCollection->findByPlayerId($playerId)->isSome();
        Assert::assertTrue(! $exists, "Player $playerId is a member of the game.");

        return $this;
    }

    public function hasPowerWithOrders(PowerId $powerId, OrderCollection $orders): self
    {
        $power = $this->game->powerCollection->getByPowerId($powerId);
        $savedOrders = $power->currentPhaseData->unwrap()->orderCollection->mapOr(
            fn (OrderCollection $orderCollection) => $orderCollection->toStringArray(),
            []
        );
        foreach ($orders as $order) {
            Assert::assertContains(
                (string) $order,
                $savedOrders,
            );
        }

        return $this;
    }
}
