<?php

namespace Dnw\Game\Core\Domain\Game;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Collection\Collection;
use Dnw\Foundation\Event\AggregateEventTrait;
use Dnw\Foundation\Exception\DomainException;
use Dnw\Foundation\Rule\LazyRule;
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
use Dnw\Game\Core\Domain\Game\StateMachine\GameStateMachine;
use Dnw\Game\Core\Domain\Game\StateMachine\GameStates;
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
        public GameStateMachine $gameStateMachine,
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
            GameStateMachine::initialize(),
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
    public function canJoin(PlayerId $playerId, Option $variantPowerId, CarbonImmutable $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::EXPECTS_STATE_PLAYERS_JOINING,
                $this->gameStateMachine->currentStateIsNot(GameStates::PLAYERS_JOINING),
            ),
            new Rule(
                GameRules::PLAYER_ALREADY_JOINED,
                $this->powerCollection->containsPlayer($playerId),
            ),
            new Rule(
                GameRules::POWER_ALREADY_FILLED,
                ! $this->randomPowerAssignments
                && $variantPowerId->map(
                    fn (VariantPowerId $variantPowerId) => $this->powerCollection->hasPowerFilled($variantPowerId)
                )->getOrElse(false),
            ),
            new Rule(
                GameRules::JOIN_LENGTH_IS_EXCEEDED,
                $this->gameStartTiming->joinLengthExceeded($currentTime),
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

    public function canBeStarted(CarbonImmutable $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::EXPECTS_STATE_PLAYERS_JOINING,
                $this->gameStateMachine->currentStateIsNot(GameStates::PLAYERS_JOINING),
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
        $this->handleGameStarting($currentTime);
        $this->handleGameJoinLengthExceeded($currentTime);

    }

    private function handleGameStarting(CarbonImmutable $currentTime): void
    {
        if ($this->canBeStarted($currentTime)->passes()) {
            $this->phasesInfo->currentPhase->get()->adjudicationTime = Some::create($this->adjudicationTiming->calculateNextAdjudication($currentTime));
            $this->pushEvent(new GameStartedEvent());
            $this->gameStateMachine->transitionTo(GameStates::ORDER_SUBMISSION);
        }

    }

    private function handleGameJoinLengthExceeded(CarbonImmutable $currentTime): void
    {
        if ($this->gameStartTiming->joinLengthExceeded($currentTime)) {
            $this->pushEvent(new GameJoinTimeExceededEvent());
            $this->gameStateMachine->transitionTo(GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE);
        }
    }

    public function submitOrders(PlayerId $playerId, OrderCollection $orderCollection, bool $markAsReady, CarbonImmutable $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot submit orders for game {$this->gameId}",
            $this->canSubmitOrders($playerId, $currentTime)
        );

        $power = $this->powerCollection->getByPlayerId($playerId);
        $currentPhaseData = $power->currentPhaseData->get();

        if ($orderCollection->isEmpty()) {
            throw new DomainException("Power $power->powerId cannot submit empty orders for game $this->gameId");
        }

        $ordersChanged = $currentPhaseData->orderCollection->map(
            fn (OrderCollection $oc) => ! $oc->hasSameContents($oc)
        )->getOrElse(true);
        if (! $ordersChanged) {
            throw new DomainException("Power $power->powerId has already submitted orders exactly the same orders for game {$this->gameId}");
        }

        $power->submitOrders($orderCollection, $markAsReady);

        $this->pushEvent(new OrdersSubmittedEvent());

        $this->adjudicateGameIfConditionsAreFulfilled($currentTime);
    }

    public function markOrderStatus(PlayerId $playerId, bool $orderStatus, CarbonImmutable $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot mark order status for game {$this->gameId}",
            $this->canMarkOrderStatus($playerId, $currentTime)
        );

        $power = $this->powerCollection->getByPlayerId($playerId);
        $orderStatusHasChanged = $power->ordersMarkedAsReady() !== $orderStatus;

        $power->markOrderStatus($orderStatus);

        if ($orderStatusHasChanged) {
            if ($orderStatus) {
                $this->pushEvent(new PhaseMarkedAsReadyEvent());
            } else {
                $this->pushEvent(new PhaseMarkedAsNotReadyEvent());
            }
        }

        $this->adjudicateGameIfConditionsAreFulfilled($currentTime);
    }

    public function canMarkOrderStatus(PlayerId $playerId, CarbonImmutable $currentTime): Ruleset
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

    public function canSubmitOrders(PlayerId $playerId, CarbonImmutable $currentTime): Ruleset
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

    public function adjudicateGameIfConditionsAreFulfilled(CarbonImmutable $currentTime): void
    {
        if ($this->isReadyForAdjudication($currentTime)) {
            // TODO: Add NMRs from powers that did not submit orders even if they had to
            $this->pushEvent(new GameReadyForAdjudicationEvent());
            $this->gameStateMachine->transitionTo(GameStates::ADJUDICATING);
        }
    }

    public function canAdjudicate(CarbonImmutable $currentTime): Ruleset
    {
        return new Ruleset(
            new Rule(
                GameRules::EXPECTS_STATE_ADJUDICATING,
                $this->gameStateMachine->currentStateIsNot(GameStates::ADJUDICATING),
            )
        );
    }

    private function isReadyForAdjudication(CarbonImmutable $currentTime): bool
    {
        return $this->adjudicationTimeIsExpired($currentTime)
            || $this->powerCollection->every(fn (Power $power) => $power->readyForAdjudication());
    }

    private function adjudicationTimeIsExpired(CarbonImmutable $currentTime): bool
    {
        return $this->phasesInfo->currentPhase->map(
            fn (Phase $phase) => $phase->adjudicationTimeIsExpired($currentTime)
        )->getOrElse(false);
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

        $hasWinners = $this->powerCollection->filter(
            fn (Power $power) => $power->currentPhaseData->map(
                fn (PhasePowerData $data) => $data->isWinner
            )->getOrElse(false)
        )->count() > 0;
        if ($hasWinners) {
            $this->pushEvent(new GameFinishedEvent());
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

        $phasePowerCollection->each(
            fn (InitialAdjudicationPowerDataDto $data) => $this->powerCollection->getByPowerId($data->powerId)->persistInitialPhase(
                $data->phasePowerData
            )
        );

        $this->pushEvent(new GameInitializedEvent());
        $this->gameStateMachine->transitionTo(GameStates::PLAYERS_JOINING);
    }
}
