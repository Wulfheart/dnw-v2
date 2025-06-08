<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Domain\Adapter\RandomNumberGenerator\RandomNumberGenerator;
use Dnw\Game\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Domain\Adapter\TimeProvider\LaravelTimeProvider;
use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Dto\AdjudicationPowerDataDto;
use Dnw\Game\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Domain\Game\Entity\Power;
use Dnw\Game\Domain\Game\Game;
use Dnw\Game\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseName;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Variant;
use Exception;
use Wulfheart\Option\Option;

/**
 * @codeCoverageIgnore
 */
class GameBuilder
{
    private function __construct(
        private Game $game,
        private TimeProviderInterface $timeProvider,
        private bool $releaseEventsBeforeBuild = true,
    ) {}

    public static function initialize(
        bool $randomAssignments = false,
        bool $startWhenReady = true,
        ?AdjudicationTiming $adjudicationTiming = null,
        ?GameStartTiming $gameStartTiming = null,
        ?Variant $variant = null,
        ?PlayerId $playerId = null,
        ?GameId $gameId = null,
        ?GameName $gameName = null,
        ?TimeProviderInterface $timeProvider = null,
    ): self {
        if ($variant === null) {
            $variantData = GameVariantDataFactory::fromVariant(VariantFactory::standard());
        } else {
            $variantData = GameVariantDataFactory::fromVariant($variant);
        }

        $rng = new RandomNumberGenerator();
        $game = Game::create(
            $gameId ?? GameId::new(),
            $gameName ?? GameName::fromString('Test Game'),
            $adjudicationTiming ?? AdjudicationTimingFactory::build(),
            $gameStartTiming ?? GameStartTimingFactory::build(startWhenReady: $startWhenReady),
            $variantData,
            $randomAssignments,
            $playerId ?? PlayerId::new(),
            Option::some($variantData->variantPowerIdCollection->getOffset(0)),
            ($rng)->generate(...),
        );

        $timeProvider ??= new FakeTimeProvider(new DateTime());

        return new self(
            $game,
            $timeProvider
        );

    }

    public function storeInitialAdjudication(): self
    {
        $c = $this->game->powerCollection->map(fn (Power $power) => new InitialAdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(
                true,
                false,
                Count::fromInt(5),
                Count::fromInt(5),
            ),
        ));

        $this->game->applyInitialAdjudication(
            PhaseTypeEnum::MOVEMENT,
            PhaseName::fromString('Spring 1901'),
            $c,
            $this->timeProvider->getCurrentTime(),
        );

        return $this;
    }

    public function join(?PlayerId $playerId = null): self
    {
        if (! $this->game->gameStateMachine->hasCurrentState(GameStates::PLAYERS_JOINING)) {
            $this->storeInitialAdjudication();
        }
        $powerToJoin = $this->game->powerCollection->getUnassignedPowers()->getOffset(0);

        $this->game->join(
            $playerId ?? PlayerId::new(),
            Option::some($powerToJoin->variantPowerId),
            $this->timeProvider->getCurrentTime(),
            new RandomNumberGenerator()->generate(...)
        );

        return $this;
    }

    public function makeFull(): self
    {
        if (! $this->game->gameStateMachine->hasCurrentState(GameStates::PLAYERS_JOINING)) {
            $this->storeInitialAdjudication();
        }
        while ($this->game->powerCollection->hasAvailablePowers()) {
            $this->join();
        }

        return $this;
    }

    public function fillUntilOnePowerLeft(): self
    {
        while ($this->game->powerCollection->getUnassignedPowers()->count() > 1) {
            $this->join();
        }

        return $this;
    }

    public function abandon(): self
    {
        foreach ($this->game->powerCollection as $power) {
            if ($power->playerId->isSome()) {
                $this->game->leave(
                    $power->playerId->unwrap(),
                );
            }
        }

        return $this;
    }

    public function start(): self
    {
        $this->makeFull();

        return $this;
    }

    public function markOnePowerAsReady(): self
    {
        $power = $this->game->powerCollection->findBy(
            fn (Power $power) => $power->playerId->isSome()
                && $power->ordersNeeded()
        )->unwrap();

        $this->game->markOrderStatus(
            $power->playerId->unwrap(),
            true,
            new DateTime(),
        );

        return $this;
    }

    public function markAllPowersAsReady(): self
    {
        $powers = $this->game->powerCollection->filter(
            fn (Power $power) => $power->ordersNeeded() && ! $power->ordersMarkedAsReady()
        );
        foreach ($powers as $power) {
            $this->game->markOrderStatus(
                $power->playerId->unwrap(),
                true,
                new DateTime(),
            );
        }

        return $this;
    }

    public function markAllButOnePowerAsReady(): self
    {
        $powers = $this->game->powerCollection->filter(
            fn (Power $power) => $power->ordersNeeded() && ! $power->ordersMarkedAsReady()
        );

        for ($i = 0; $i < $powers->count() - 1; $i++) {
            $power = $powers->getOffset($i);
            $this->game->markOrderStatus(
                $power->playerId->unwrap(),
                true,
                new DateTime(),
            );
        }

        return $this;
    }

    public function transitionToAdjudicating(): self
    {
        if (! $this->game->gameStateMachine->hasCurrentState(GameStates::ORDER_SUBMISSION)) {
            $this->start();
        }
        $this->markAllPowersAsReady();
        if ($this->game->gameStateMachine->currentStateIsNot(GameStates::ADJUDICATING)) {
            throw new Exception('Game is not in the correct state to transition to adjudicating');
        }

        return $this;
    }

    public function submitOrders(bool $markAsReady): self
    {
        $powersWithOrders = $this->game->powerCollection->filter(
            fn (Power $power) => $power->ordersNeeded()
        );

        /** @var Power $power */
        foreach ($powersWithOrders as $power) {
            $this->game->submitOrders(
                $power->playerId->unwrap(),
                OrderCollection::fromStringArray(['ORDER: ' . (string) $power->playerId->unwrap()]),
                $markAsReady,
                new DateTime(),
            );
        }

        return $this;
    }

    public function defeatPower(?PowerId $powerId = null): self
    {
        if ($powerId === null) {
            $powerToDefeat = $this->game->powerCollection->filter(
                fn (Power $power) => $power->ordersNeeded()
            )->first();
        } else {
            $powerToDefeat = $this->game->powerCollection->findBy(
                fn (Power $power) => $power->powerId === $powerId
            )->unwrap();
        }

        $currentPhaseData = $powerToDefeat->currentPhaseData->unwrap();
        $currentPhaseData->supplyCenterCount = Count::fromInt(0);
        $currentPhaseData->unitCount = Count::fromInt(0);
        $currentPhaseData->ordersNeeded = false;

        return $this;
    }

    public function finish(): self
    {
        if (! $this->game->gameStateMachine->hasCurrentState(GameStates::ADJUDICATING)) {
            $this->transitionToAdjudicating();
        }
        $this->game->powerCollection->map(fn (Power $power) => new AdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
            new OrderCollection()
        ));
        $powerWhichWillWin = $this->game->powerCollection->first();

        $adjudicationPowerDataCollection = $this->game->powerCollection->map(fn (Power $power) => new AdjudicationPowerDataDto(
            $power->powerId,
            new NewPhaseData(true, false, Count::fromInt(1), Count::fromInt(1)),
            new OrderCollection()
        ));

        /** @var AdjudicationPowerDataDto $defeatedPhasePowerData */
        $defeatedPhasePowerData = $adjudicationPowerDataCollection->findBy(fn (AdjudicationPowerDataDto $adjudicationPowerData) => $adjudicationPowerData->powerId === $powerWhichWillWin->powerId)->unwrap();
        $defeatedPhasePowerData->newPhaseData->isWinner = true;

        $this->game->applyAdjudication(
            PhaseTypeEnum::MOVEMENT,
            PhaseName::fromString('Spring 1901'),
            $adjudicationPowerDataCollection,
            new DateTime()
        );

        return $this;
    }

    public function doNotReleaseEvents(): self
    {
        $this->releaseEventsBeforeBuild = false;

        return $this;
    }

    public function build(): Game
    {
        if ($this->releaseEventsBeforeBuild) {
            $this->game->releaseEvents();
        }

        return $this->game;
    }
}
