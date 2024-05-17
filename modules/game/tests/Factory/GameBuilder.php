<?php

namespace Dnw\Game\Tests\Factory;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Core\Infrastructure\Adapter\RandomNumberGenerator;
use Exception;
use PhpOption\Some;

class GameBuilder
{
    private function __construct(
        private Game $game,
    ) {

    }

    public static function initialize(
        bool $randomAssignments = false,
        bool $startWhenReady = true,
    ): self {
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
            )
        );
        $rng = new RandomNumberGenerator();
        $game = Game::create(
            GameId::new(),
            GameName::fromString('Test Game'),
            AdjudicationTimingFactory::build(),
            GameStartTimingFactory::build(startWhenReady: $startWhenReady),
            $variantData,
            $randomAssignments,
            PlayerId::new(),
            Some::create($variantData->variantPowerIdCollection->getOffset(0)),
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
            new CarbonImmutable(),
        );

        return $this;
    }

    public function join(?PlayerId $playerId = null): self
    {
        $powerToJoin = $this->game->powerCollection->getUnassignedPowers()->getOffset(0);

        $this->game->join(
            $playerId ?? PlayerId::new(),
            Some::create($powerToJoin->variantPowerId),
            new CarbonImmutable(),
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
            if ($power->playerId->isDefined()) {
                $this->game->leave(
                    $power->playerId->get(),
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
            fn (Power $power) => $power->playerId->isDefined()
                && $power->ordersNeeded()
        )->get();

        $this->game->markOrderStatus(
            $power->playerId->get(),
            true,
            new CarbonImmutable(),
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
                $power->playerId->get(),
                true,
                new CarbonImmutable(),
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
                $power->playerId->get(),
                true,
                new CarbonImmutable(),
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

    public function build(): Game
    {
        $this->game->releaseEvents();

        return $this->game;
    }
}
