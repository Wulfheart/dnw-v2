<?php

namespace Dnw\Game\Tests\Factory;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Core\Domain\Variant\Variant;
use Dnw\Game\Core\Infrastructure\Adapter\RandomNumberGenerator;
use Exception;
use Std\Option;

class GameBuilder
{
    private function __construct(
        private Game $game,
        private bool $releaseEventsBeforeBuild = true,
    ) {

    }

    public static function initialize(
        bool $randomAssignments = false,
        bool $startWhenReady = true,
        ?AdjudicationTiming $adjudicationTiming = null,
        ?GameStartTiming $gameStartTiming = null,
        ?Variant $variant = null,
    ): self {
        if ($variant === null) {
            $variantData = GameVariantDataFactory::build(
                variantPowerIdCollection: VariantPowerIdCollection::build(
                    VariantPowerId::new(),
                    VariantPowerId::new(),
                    VariantPowerId::new(),
                    VariantPowerId::new(),
                    VariantPowerId::new(),
                    VariantPowerId::new(),
                    VariantPowerId::new(),
                    VariantPowerId::new(),
                ),
                defaultSupplyCenterCountToWin: Count::fromInt(18),
            );
        } else {
            $variantData = GameVariantDataFactory::fromVariant($variant);
        }

        $rng = new RandomNumberGenerator();
        $game = Game::create(
            GameId::new(),
            GameName::fromString('Test Game'),
            $adjudicationTiming ?? AdjudicationTimingFactory::build(),
            $gameStartTiming ?? GameStartTimingFactory::build(startWhenReady: $startWhenReady),
            $variantData,
            $randomAssignments,
            PlayerId::new(),
            Option::some($variantData->variantPowerIdCollection->getOffset(0)),
            ($rng)->generate(...),
        );

        return new self(
            $game,
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
            $c,
            new DateTime(),
        );

        return $this;
    }

    public function join(?PlayerId $playerId = null): self
    {
        $powerToJoin = $this->game->powerCollection->getUnassignedPowers()->getOffset(0);

        $this->game->join(
            $playerId ?? PlayerId::new(),
            Option::some($powerToJoin->variantPowerId),
            new DateTime(),
            (new RandomNumberGenerator())->generate(...)
        );

        return $this;
    }

    public function makeFull(): self
    {
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

    public function defeatPower(): self
    {
        $powerToDefeat = $this->game->powerCollection->filter(
            fn (Power $power) => $power->ordersNeeded()
        )->first();

        $currentPhaseData = $powerToDefeat->currentPhaseData->unwrap();
        $currentPhaseData->supplyCenterCount = Count::fromInt(0);
        $currentPhaseData->unitCount = Count::fromInt(0);
        $currentPhaseData->ordersNeeded = false;

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
