<?php

namespace Dnw\Game\Domain\Game;

use Dnw\Foundation\Aggregate\AggregateVersion;
use Dnw\Foundation\Collection\Collection;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Event\AggregateEventTrait;
use Dnw\Foundation\Exception\DomainException;
use Dnw\Foundation\Rule\LazyRule;
use Dnw\Foundation\Rule\Rule;
use Dnw\Foundation\Rule\Ruleset;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Collection\PowerCollection;
use Dnw\Game\Domain\Game\Dto\AdjudicationPowerDataDto;
use Dnw\Game\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Domain\Game\Entity\Phase;
use Dnw\Game\Domain\Game\Entity\Power;
use Dnw\Game\Domain\Game\Event\GameAbandonedEvent;
use Dnw\Game\Domain\Game\Event\GameAdjudicatedEvent;
use Dnw\Game\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Domain\Game\Event\GameFinishedEvent;
use Dnw\Game\Domain\Game\Event\GameInitializedEvent;
use Dnw\Game\Domain\Game\Event\GameJoinTimeExceededEvent;
use Dnw\Game\Domain\Game\Event\GameReadyForAdjudicationEvent;
use Dnw\Game\Domain\Game\Event\GameStartedEvent;
use Dnw\Game\Domain\Game\Event\OrdersSubmittedEvent;
use Dnw\Game\Domain\Game\Event\PhaseMarkedAsNotReadyEvent;
use Dnw\Game\Domain\Game\Event\PhaseMarkedAsReadyEvent;
use Dnw\Game\Domain\Game\Event\PlayerJoinedEvent;
use Dnw\Game\Domain\Game\Event\PlayerLeftEvent;
use Dnw\Game\Domain\Game\Event\PowerDefeatedEvent;
use Dnw\Game\Domain\Game\Exception\RulesetHandler;
use Dnw\Game\Domain\Game\Rule\GameRules;
use Dnw\Game\Domain\Game\StateMachine\GameStateMachine;
use Dnw\Game\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseName;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Domain\Game\ValueObject\Phases\PhasesInfo;
use Dnw\Game\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Wulfheart\Option\Option;

class Game
{
    use AggregateEventTrait;

    /**
     * @param  array<object>  $events
     */
    public function __construct(
        public GameId $gameId,
        public GameName $name,
        public GameStateMachine $gameStateMachine,
        public AdjudicationTiming $adjudicationTiming,
        public GameStartTiming $gameStartTiming,
        public bool $randomPowerAssignments,
        public GameVariantData $variant,
        public PowerCollection $powerCollection,
        public PhasesInfo $phasesInfo,
        public AggregateVersion $version,
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
            $powers->getByVariantPowerId($variantPowerId->unwrap())->assign($playerId);
        }

        return new self(
            $gameId,
            $name,
            GameStateMachine::initialize(),
            $adjudicationTiming,
            $gameStartTiming,
            $randomPowerAssignment,
            $variantData,
            $powers,
            PhasesInfo::initialize(),
            AggregateVersion::initial(),
            [new GameCreatedEvent($gameId->toId(), $playerId->toId())],
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
        DateTime $currentTime,
        callable $randomNumberGenerator
    ): void {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot join game {$this->gameId}",
            $this->canJoin($playerId, $variantPowerId, $currentTime)
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
            $power = $this->powerCollection->getByVariantPowerId($randomPower->variantPowerId);
            $power->assign($playerId);
        } else {
            $power = $this->powerCollection->getByVariantPowerId($variantPowerId->unwrap());
            $power->assign($playerId);
        }

        $this->pushEvent(new PlayerJoinedEvent($this->gameId->toId(), $power->powerId->toId()));

        $this->startGameIfFullAndStartWhenReady($currentTime);
    }

    /**
     * @param  Option<VariantPowerId>  $variantPowerId
     */
    public function canJoin(PlayerId $playerId, Option $variantPowerId, DateTime $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::POWER_ALREADY_FILLED,
                ! $this->randomPowerAssignments
                && $variantPowerId->mapOr(
                    fn (VariantPowerId $variantPowerId) => $this->powerCollection->hasPowerFilled($variantPowerId),
                    false
                ),
            ),
            ...$this->canBeJoined($playerId, $currentTime)->rules()
        );
    }

    public function canBeJoined(PlayerId $playerId, DateTime $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::JOIN_LENGTH_IS_EXCEEDED,
                $this->gameStartTiming->joinLengthExceeded($currentTime),
            ),
            new Rule(
                GameRules::PLAYER_ALREADY_JOINED,
                $this->powerCollection->containsPlayer($playerId),
            ),
            new Rule(
                GameRules::EXPECTS_STATE_PLAYERS_JOINING,
                $this->gameStateMachine->currentStateIsNot(GameStates::PLAYERS_JOINING),
            )
        );
    }

    public function leave(PlayerId $playerId): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot leave game {$this->gameId}",
            $this->canLeave($playerId)
        );

        $this->powerCollection->getByPlayerId($playerId)->unassign();
        $this->pushEvent(new PlayerLeftEvent($this->gameId->toId(), $playerId->toId()));
        if ($this->powerCollection->hasNoAssignedPowers()) {
            $this->pushEvent(new GameAbandonedEvent($this->gameId->toId()));
            $this->gameStateMachine->transitionTo(GameStates::ABANDONED);
        }
    }

    public function canLeave(PlayerId $playerId): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::EXPECTS_STATE_PLAYERS_JOINING,
                $this->gameStateMachine->currentStateIsNot(GameStates::PLAYERS_JOINING),
            ),
            new Rule(
                GameRules::PLAYER_NOT_IN_GAME,
                $this->powerCollection->doesNotContainPlayer($playerId),
            ),
        );
    }

    private function startGameIfFullAndStartWhenReady(DateTime $currentTime): void
    {
        if ($this->gameStartTiming->startWhenReady && $this->powerCollection->hasAllPowersFilled()) {
            $this->startGame($currentTime);
        }
    }

    private function startGame(DateTime $currentTime): void
    {
        $this->phasesInfo->currentPhase->unwrap()->adjudicationTime = Option::some($this->adjudicationTiming->calculateNextAdjudication($currentTime));
        $this->pushEvent(new GameStartedEvent($this->gameId->toId()));
        $this->gameStateMachine->transitionTo(GameStates::ORDER_SUBMISSION);
    }

    public function handleGameJoinLengthExceeded(DateTime $currentTime): void
    {
        if ($this->gameStartTiming->joinLengthExceeded($currentTime)) {
            if ($this->powerCollection->hasAllPowersFilled()) {
                $this->startGame($currentTime);
            } else {
                $this->pushEvent(new GameJoinTimeExceededEvent($this->gameId->toId()));
                $this->gameStateMachine->transitionTo(GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE);
            }
        }
    }

    public function submitOrders(PlayerId $playerId, OrderCollection $orderCollection, bool $markAsReady, DateTime $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot submit orders for game {$this->gameId}",
            $this->canSubmitOrders($playerId, $currentTime)
        );

        $power = $this->powerCollection->getByPlayerId($playerId);
        $currentPhaseData = $power->currentPhaseData->unwrap();

        if ($orderCollection->isEmpty()) {
            throw new DomainException("Power $power->powerId cannot submit empty orders for game $this->gameId");
        }

        $ordersChanged = $currentPhaseData->orderCollection->mapOr(
            fn (OrderCollection $oc) => ! $oc->hasSameContents($oc),
            true
        );
        if (! $ordersChanged) {
            throw new DomainException("Power $power->powerId has already submitted orders exactly the same orders for game {$this->gameId}");
        }

        $power->submitOrders($orderCollection, $markAsReady);

        $this->pushEvent(new OrdersSubmittedEvent(
            $this->gameId->toId(),
            $this->phasesInfo->currentPhase->unwrap()->phaseId->toId(),
            $power->powerId->toId(),
            $markAsReady
        ));

        $this->adjudicateGameIfConditionsAreFulfilled($currentTime);
    }

    public function markOrderStatus(PlayerId $playerId, bool $orderStatus, DateTime $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot mark order status for game {$this->gameId}",
            $this->canMarkOrderStatus($playerId, $currentTime)
        );

        $power = $this->powerCollection->getByPlayerId($playerId);
        $orderStatusHasChanged = $power->ordersMarkedAsReady() !== $orderStatus;

        if (! $orderStatusHasChanged) {
            throw new DomainException("Order status for power $power->powerId has not changed for game {$this->gameId}");
        }

        $power->markOrderStatus($orderStatus);

        if ($orderStatus) {
            $this->pushEvent(new PhaseMarkedAsReadyEvent(
                $this->gameId->toId(),
                $this->phasesInfo->currentPhase->unwrap()->phaseId->toId(),
                $power->powerId->toId()
            ));
        } else {
            $this->pushEvent(new PhaseMarkedAsNotReadyEvent(
                $this->gameId->toId(),
                $this->phasesInfo->currentPhase->unwrap()->phaseId->toId(),
                $power->powerId->toId()
            ));
        }

        $this->adjudicateGameIfConditionsAreFulfilled($currentTime);
    }

    public function canMarkOrderStatus(PlayerId $playerId, DateTime $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::PLAYER_NOT_IN_GAME,
                $this->powerCollection->doesNotContainPlayer($playerId),
                new LazyRule(
                    GameRules::POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS,
                    fn () => ! $this->powerCollection->getByPlayerId($playerId)->ordersNeeded(),
                )
            ),
            new Rule(
                GameRules::EXPECTS_STATE_ORDER_SUBMISSION,
                $this->gameStateMachine->currentStateIsNot(GameStates::ORDER_SUBMISSION),
            )
        );
    }

    public function canSubmitOrders(PlayerId $playerId, DateTime $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::EXPECTS_STATE_ORDER_SUBMISSION,
                $this->gameStateMachine->currentStateIsNot(GameStates::ORDER_SUBMISSION)
            ),
            new Rule(
                GameRules::PLAYER_NOT_IN_GAME,
                $this->powerCollection->doesNotContainPlayer($playerId),
                new LazyRule(
                    GameRules::POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS,
                    fn () => ! $this->powerCollection->getByPlayerId($playerId)->ordersNeeded(),
                ),
                new LazyRule(
                    GameRules::ORDERS_ALREADY_MARKED_AS_READY,
                    fn () => $this->powerCollection->getByPlayerId($playerId)->ordersMarkedAsReady()
                )
            ),
            new Rule(
                GameRules::GAME_PHASE_TIME_EXCEEDED,
                $this->adjudicationTimeIsExpired($currentTime)
            )
        );
    }

    public function adjudicateGameIfConditionsAreFulfilled(DateTime $currentTime): void
    {
        if ($this->isReadyForAdjudication($currentTime)) {
            // TODO: Add NMRs from powers that did not submit orders even if they had to
            $this->pushEvent(new GameReadyForAdjudicationEvent(
                $this->gameId->toId(),
            ));
            $this->gameStateMachine->transitionTo(GameStates::ADJUDICATING);
        }
    }

    public function canAdjudicate(DateTime $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::EXPECTS_STATE_ADJUDICATING,
                $this->gameStateMachine->currentStateIsNot(GameStates::ADJUDICATING),
            )
        );
    }

    private function isReadyForAdjudication(DateTime $currentTime): bool
    {
        return $this->adjudicationTimeIsExpired($currentTime)
            || $this->powerCollection->every(fn (Power $power) => $power->readyForAdjudication());
    }

    private function adjudicationTimeIsExpired(DateTime $currentTime): bool
    {
        return $this->phasesInfo->currentPhase->mapOr(
            fn (Phase $phase) => $phase->adjudicationTimeIsExpired($currentTime),
            false
        );
    }

    /**
     * @param  Collection<AdjudicationPowerDataDto>  $adjudicationPowerDataCollection
     *
     * @throws DomainException
     */
    public function applyAdjudication(
        PhaseTypeEnum $phaseType,
        PhaseName $phaseName,
        Collection $adjudicationPowerDataCollection,
        DateTime $currentTime
    ): void {
        RulesetHandler::throwConditionally(
            "Game {$this->gameId} cannot be adjudicated",
            $this->canAdjudicate($currentTime)
        );

        $nextAdjudication = $this->adjudicationTiming->calculateNextAdjudication($currentTime);

        $newPhase = new Phase(
            PhaseId::new(),
            $phaseType,
            $phaseName,
            Option::some($nextAdjudication),
        );

        /** @var AdjudicationPowerDataDto $data */
        foreach ($adjudicationPowerDataCollection as $data) {
            $power = $this->powerCollection->getByPowerId($data->powerId);
            $isAlreadyDefeated = $power->isDefeated();

            $this->powerCollection->getByPowerId($data->powerId)->proceedToNextPhase(
                new PhasePowerData(
                    $data->newPhaseData->ordersNeeded,
                    false,
                    $data->newPhaseData->isWinner,
                    $data->newPhaseData->supplyCenterCount,
                    $data->newPhaseData->unitCount,
                    Option::none(),
                ),
                $data->appliedOrders
            );

            if (! $isAlreadyDefeated && $power->isDefeated()) {
                $this->pushEvent(new PowerDefeatedEvent(
                    $this->gameId->toId(),
                    $power->powerId->toId(),
                    $this->phasesInfo->count->int()
                ));
            }
        }

        $this->phasesInfo->proceedToNewPhase($newPhase);

        $this->pushEvent(new GameAdjudicatedEvent($this->gameId->toId()));

        $hasWinners = $this->powerCollection->filter(
            fn (Power $power) => $power->currentPhaseData->mapOr(
                fn (PhasePowerData $data) => $data->isWinner,
                false
            )
        )->count() > 0;

        if ($hasWinners) {
            $this->pushEvent(new GameFinishedEvent($this->gameId->toId(), $this->phasesInfo->count->int()));
            $this->gameStateMachine->transitionTo(GameStates::FINISHED);
        } else {
            $this->gameStateMachine->transitionTo(GameStates::ORDER_SUBMISSION);
        }
    }

    public function canApplyInitialAdjudication(): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::EXPECTS_STATE_CREATED,
                $this->gameStateMachine->currentStateIsNot(GameStates::CREATED)
            )
        );
    }

    /**
     * @param  Collection<InitialAdjudicationPowerDataDto>  $phasePowerCollection
     */
    public function applyInitialAdjudication(
        PhaseTypeEnum $phaseType,
        PhaseName $phaseName,
        Collection $phasePowerCollection,
        DateTime $currentTime
    ): void {
        RulesetHandler::throwConditionally(
            "Game {$this->gameId} cannot apply initial adjudication",
            $this->canApplyInitialAdjudication()
        );

        $newPhase = new Phase(
            PhaseId::new(),
            $phaseType,
            $phaseName,
            Option::none(),
        );

        $this->phasesInfo->setInitialPhase($newPhase);

        foreach ($phasePowerCollection as $data) {
            $power = $this->powerCollection->getByPowerId($data->powerId);
            $power->persistInitialPhase(
                new PhasePowerData(
                    $data->phasePowerData->ordersNeeded,
                    false,
                    $data->phasePowerData->isWinner,
                    $data->phasePowerData->supplyCenterCount,
                    $data->phasePowerData->unitCount,
                    Option::none(),
                )
            );

        }

        $this->pushEvent(new GameInitializedEvent($this->gameId->toId()));
        $this->gameStateMachine->transitionTo(GameStates::PLAYERS_JOINING);
    }
}
