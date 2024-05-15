<?php

namespace Dnw\Game\Core\Domain\Game;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Collection\Collection;
use Dnw\Foundation\Event\AggregateEventTrait;
use Dnw\Foundation\Exception\DomainException;
use Dnw\Foundation\Rule\Rule;
use Dnw\Foundation\Rule\Ruleset;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Collection\PowerCollection;
use Dnw\Game\Core\Domain\Game\Dto\AdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\Event\GameAbandonedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameAdjudicatedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameFinishedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameInitializedEvent;
use Dnw\Game\Core\Domain\Game\Event\GameJoinTimeExceededEvent;
use Dnw\Game\Core\Domain\Game\Event\GameReadyForAdjudicationEvent;
use Dnw\Game\Core\Domain\Game\Event\GameStartedEvent;
use Dnw\Game\Core\Domain\Game\Event\OrdersSubmittedEvent;
use Dnw\Game\Core\Domain\Game\Event\PhaseMarkedAsNotReadyEvent;
use Dnw\Game\Core\Domain\Game\Event\PhaseMarkedAsReadyEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerJoinedEvent;
use Dnw\Game\Core\Domain\Game\Event\PlayerLeftEvent;
use Dnw\Game\Core\Domain\Game\Exception\RulesetHandler;
use Dnw\Game\Core\Domain\Game\Rule\GameRules;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\Game\ValueObject\Phases\PhasesInfo;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class Game
{
    use AggregateEventTrait;

    /**
     * @param  array<object>  $events
     */
    public function __construct(
        public GameId $gameId,
        public GameName $name,
        public AdjudicationTiming $adjudicationTiming,
        public GameStartTiming $gameStartTiming,
        public bool $randomPowerAssignments,
        public GameVariantData $variant,
        public PowerCollection $powerCollection,
        public PhasesInfo $phasesInfo,
        array $events = [],
    ) {
        $this->events = $events;
    }

    /**
     * @param  callable(int $lower, int $upper): int  $randomNumberGenerator
     * @param  Option<VariantPowerId>  $variantPowerId
     */
    public static function create(
        GameId $gameId,
        GameName $name,
        AdjudicationTiming $adjudicationTiming,
        GameStartTiming $gameStartTiming,
        GameVariantData $variantData,
        bool $randomPowerAssignment,
        PlayerId $playerId,
        Option $variantPowerId,
        callable $randomNumberGenerator
    ): self {

        $powers = PowerCollection::createFromVariantPowerIdCollection(
            $variantData->variantPowerIdCollection
        );
        if ($randomPowerAssignment) {
            /** @var int<0,max> $randomIndex */
            $randomIndex = $randomNumberGenerator(0, $powers->count() - 1);
            $randomPower = $powers->getOffset($randomIndex);
            $powers->getByVariantPowerId($randomPower->variantPowerId)->assign($playerId);
        } else {
            $powers->getByVariantPowerId($variantPowerId->get())->assign($playerId);
        }

        return new self(
            $gameId,
            $name,
            $adjudicationTiming,
            $gameStartTiming,
            $randomPowerAssignment,
            $variantData,
            $powers,
            PhasesInfo::initialize(),
            [new GameCreatedEvent()]
        );
    }

    public function calculateSupplyCenterCountForWinning(): Count
    {
        return $this->variant->defaultSupplyCentersToWinCount;
    }

    /**
     * @param  Option<VariantPowerId>  $variantPowerId
     * @param  callable(int $lower, int $upper): int  $randomNumberGenerator
     *
     * @throws DomainException
     */
    public function join(
        PlayerId $playerId,
        Option $variantPowerId,
        CarbonImmutable $currentTime,
        callable $randomNumberGenerator
    ): void {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot join game {$this->gameId}",
            $this->canJoin($playerId, $variantPowerId)
        );

        if ($this->randomPowerAssignments) {
            $unassignedPowers = $this->powerCollection->getUnassignedPowers();
            // @codeCoverageIgnoreStart
            // Should be covered by the rule check
            if ($unassignedPowers->isEmpty()) {
                throw new DomainException("No available powers for player $playerId in game {$this->gameId}");
            }
            // @codeCoverageIgnoreEnd
            /** @var int<0,max> $randomPowerIndex */
            $randomPowerIndex = $randomNumberGenerator(0, $unassignedPowers->count() - 1);
            $randomPower = $unassignedPowers->getOffset($randomPowerIndex);
            $this->powerCollection->getByVariantPowerId($randomPower->variantPowerId)->assign($playerId);
        } else {
            $this->powerCollection->getByVariantPowerId($variantPowerId->get())->assign($playerId);
        }

        $this->pushEvent(new PlayerJoinedEvent());

        $this->handleGameStartingConditions($currentTime);
    }

    /**
     * @param  Option<VariantPowerId>  $variantPowerId
     */
    public function canJoin(PlayerId $playerId, Option $variantPowerId): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::HAS_BEEN_STARTED,
                $this->hasBeenStarted(),
            ),
            new Rule(
                GameRules::INITIAL_PHASE_DOES_NOT_EXIST,
                ! $this->phasesInfo->initialPhaseExists(),
            ),
            new Rule(
                GameRules::HAS_NO_AVAILABLE_POWERS,
                ! $this->powerCollection->hasAvailablePowers(),
            ),
            new Rule(
                GameRules::PLAYER_ALREADY_JOINED,
                $this->powerCollection->containsPlayer($playerId),
            ),
            new Rule(
                GameRules::POWER_ALREADY_FILLED,
                ! $this->randomPowerAssignments
                && $this->powerCollection->hasPowerFilled($variantPowerId->get()),
            ),
            new Rule(
                GameRules::HAS_BEEN_ABANDONED,
                $this->hasBeenAbandoned(),
            )
        );
    }

    /**
     * @throws DomainException
     */
    public function leave(PlayerId $playerId): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot leave game {$this->gameId}",
            $this->canLeave($playerId)
        );

        $this->powerCollection->getByPlayerId($playerId)->unassign();
        $this->pushEvent(new PlayerLeftEvent());
        if ($this->powerCollection->hasNoAssignedPowers()) {
            $this->pushEvent(new GameAbandonedEvent());
        }
    }

    public function canLeave(PlayerId $playerId): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::HAS_BEEN_STARTED,
                $this->hasBeenStarted(),
            ),
            new Rule(
                GameRules::PLAYER_NOT_IN_GAME,
                $this->powerCollection->doesNotContainPlayer($playerId),
            ),
        );
    }

    private function hasBeenStarted(): bool
    {
        return $this->phasesInfo->hasBeenStarted();
    }

    private function hasBeenFinished(): bool
    {
        return $this->hasWinners();
    }

    private function hasWinners(): bool
    {
        return $this->powerCollection->filter(
            fn (Power $power) => $power->currentPhaseData->map(
                fn (PhasePowerData $data) => $data->isWinner
            )->getOrElse(false)
        )->count() > 0;
    }

    private function hasBeenAbandoned(): bool
    {
        return $this->powerCollection->hasNoAssignedPowers();
    }

    public function canBeStarted(CarbonImmutable $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::HAS_BEEN_STARTED,
                $this->hasBeenStarted(),
            ),
            new Rule(
                GameRules::INITIAL_PHASE_DOES_NOT_EXIST,
                ! $this->phasesInfo->initialPhaseExists(),
            ),
            new Rule(
                GameRules::HAS_AVAILABLE_POWERS,
                $this->powerCollection->hasAvailablePowers(),
            ),
            new Rule(
                GameRules::GAME_NOT_MARKED_AS_READY_OR_JOIN_LENGTH_NOT_EXCEEDED,
                ! $this->gameStartTiming->startWhenReady
                && ! $this->gameStartTiming->joinLengthExceeded($currentTime),
            ),
        );
    }

    public function handleGameStartingConditions(CarbonImmutable $currentTime): void
    {
        if ($this->canBeStarted($currentTime)->passes()) {
            $this->phasesInfo->currentPhase->get()->adjudicationTime = Some::create($this->adjudicationTiming->calculateNextAdjudication($currentTime));
            $this->pushEvent(new GameStartedEvent());
        }
        if ($this->gameStartTiming->joinLengthExceeded($currentTime)) {
            $this->pushEvent(new GameJoinTimeExceededEvent());
        }
    }

    public function submitOrders(PlayerId $playerId, OrderCollection $orderCollection, bool $markAsReady, CarbonImmutable $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot submit orders for game {$this->gameId}",
            $this->canSubmitOrders($playerId, $currentTime)
        );

        $power = $this->powerCollection->getByPlayerId($playerId);

        $this->powerCollection->getByPowerId($power->powerId)->submitOrders($orderCollection, $markAsReady);

        $this->pushEvent(new OrdersSubmittedEvent());
        if ($markAsReady) {
            $this->pushEvent(new PhaseMarkedAsReadyEvent());
        }

        $this->adjudicateGameIfConditionsAreFulfilled($currentTime);
    }

    public function markOrderStatus(PlayerId $playerId, bool $orderStatus, CarbonImmutable $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot mark order status for game {$this->gameId}",
            $this->canMarkOrderStatus($playerId, $currentTime)
        );

        $power = $this->powerCollection->getByPlayerId($playerId);

        $this->powerCollection->getByPowerId($power->powerId)->markOrderStatus($orderStatus);

        if ($orderStatus) {
            $this->pushEvent(new PhaseMarkedAsReadyEvent());
        } else {
            $this->pushEvent(new PhaseMarkedAsNotReadyEvent());
        }

        $this->adjudicateGameIfConditionsAreFulfilled($currentTime);
    }

    public function canMarkOrderStatus(PlayerId $playerId, CarbonImmutable $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::PLAYER_NOT_IN_GAME,
                $this->powerCollection->doesNotContainPlayer($playerId),
                new Rule(
                    GameRules::POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS,
                    ! $this->powerCollection->getByPlayerId($playerId)->ordersMarkedAsReady(),
                )
            ),
            new Rule(
                GameRules::GAME_READY_FOR_ADJUDICATION,
                $this->canAdjudicate($currentTime)->fails(),
            )
        );
    }

    public function canSubmitOrders(PlayerId $playerId, CarbonImmutable $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::HAS_NOT_BEEN_STARTED,
                ! $this->phasesInfo->hasBeenStarted(),
            ),
            new Rule(
                GameRules::HAS_BEEN_FINISHED,
                $this->hasBeenFinished(),
            ),
            new Rule(
                GameRules::PLAYER_NOT_IN_GAME,
                $this->powerCollection->doesNotContainPlayer($playerId),
                new Rule(
                    GameRules::POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS,
                    ! $this->powerCollection->getByPlayerId($playerId)->ordersNeeded(),
                    new Rule(
                        GameRules::ORDERS_ALREADY_MARKED_AS_READY,
                        $this->powerCollection->getByPlayerId($playerId)->ordersMarkedAsReady()
                    )
                )
            ),
            new Rule(
                GameRules::GAME_READY_FOR_ADJUDICATION,
                $this->canAdjudicate($currentTime)->fails(),
            )
        );
    }

    public function adjudicateGameIfConditionsAreFulfilled(CarbonImmutable $currentTime): void
    {
        if ($this->canAdjudicate($currentTime)->passes()) {
            $this->pushEvent(new GameReadyForAdjudicationEvent());
        }
    }

    public function canAdjudicate(CarbonImmutable $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::HAS_BEEN_STARTED,
                $this->phasesInfo->hasBeenStarted(),
            ),
            new Rule(
                GameRules::HAS_NOT_BEEN_FINISHED,
                ! $this->hasBeenFinished(),
            ),
            new Rule(
                GameRules::PHASE_NOT_MARKED_AS_READY_AND_TIME_HAS_NOT_EXPIRED,
                $this->phasesInfo->currentPhase->map(
                    fn (Phase $phase) => $phase->adjudicationTimeIsExpired($currentTime)
                )->getOrElse(false)
                || ! $this->powerCollection->every(fn (Power $power) => $power->readyForAdjudication())
            )
        );
    }

    /**
     * @param  Collection<AdjudicationPowerDataDto>  $adjudicationPowerDataCollection
     *
     * @throws DomainException
     */
    public function applyAdjudication(
        PhaseTypeEnum $phaseType,
        Collection $adjudicationPowerDataCollection,
        CarbonImmutable $currentTime
    ): void {
        RulesetHandler::throwConditionally(
            "Game {$this->gameId} cannot be adjudicated",
            $this->canAdjudicate($currentTime)
        );

        $nextAdjudication = $this->adjudicationTiming->calculateNextAdjudication($currentTime);

        $newPhase = new Phase(
            PhaseId::new(),
            $phaseType,
            Some::create($nextAdjudication),
        );

        $this->phasesInfo->proceedToNewPhase($newPhase);
        foreach ($adjudicationPowerDataCollection as $adjudicationPowerData) {
            $this->powerCollection->getByPowerId($adjudicationPowerData->powerId)->proceedToNextPhase(
                $adjudicationPowerData->newPhaseData,
                $adjudicationPowerData->appliedOrders,
            );
        }

        $this->pushEvent(new GameAdjudicatedEvent());

        if ($this->hasBeenFinished()) {
            $this->pushEvent(new GameFinishedEvent());
        }
    }

    public function canApplyInitialAdjudication(): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::PHASE_IS_ALREADY_SET,
                $this->phasesInfo->currentPhase->isDefined(),
            )
        );
    }

    /**
     * @param  Collection<InitialAdjudicationPowerDataDto>  $phasePowerCollection
     *
     * @throws DomainException
     */
    public function applyInitialAdjudication(
        PhaseTypeEnum $phaseType,
        Collection $phasePowerCollection,
        CarbonImmutable $currentTime
    ): void {
        RulesetHandler::throwConditionally(
            "Game {$this->gameId} cannot apply initial adjudication",
            $this->canApplyInitialAdjudication()
        );

        $newPhase = new Phase(
            PhaseId::new(),
            $phaseType,
            None::create(),
        );

        $this->phasesInfo->setInitialPhase($newPhase);

        $this->pushEvent(new GameInitializedEvent());
    }
}
