<?php

namespace Dnw\Game\Core\Domain\Aggregate;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Event\AggregateEventTrait;
use Dnw\Foundation\Rule\Rule;
use Dnw\Foundation\Rule\Ruleset;
use Dnw\Game\Core\Domain\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Collection\PowerCollection;
use Dnw\Game\Core\Domain\Entity\MessageMode;
use Dnw\Game\Core\Domain\Entity\Variant;
use Dnw\Game\Core\Domain\Event\GameAbandonedEvent;
use Dnw\Game\Core\Domain\Event\GameCreatedEvent;
use Dnw\Game\Core\Domain\Event\GameReadyForAdjudicationEvent;
use Dnw\Game\Core\Domain\Event\GameStartedEvent;
use Dnw\Game\Core\Domain\Event\OrdersSubmittedEvent;
use Dnw\Game\Core\Domain\Event\PhaseMarkedAsNotReadyEvent;
use Dnw\Game\Core\Domain\Event\PhaseMarkedAsReadyEvent;
use Dnw\Game\Core\Domain\Event\PlayerJoinedEvent;
use Dnw\Game\Core\Domain\Event\PlayerLeftEvent;
use Dnw\Game\Core\Domain\Exception\DomainException;
use Dnw\Game\Core\Domain\Exception\RulesetHandler;
use Dnw\Game\Core\Domain\Rule\GameRules;
use Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\ValueObject\Phases\PhasesInfo;
use Dnw\Game\Core\Domain\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerId;
use PhpOption\Option;

class Game
{
    use AggregateEventTrait;

    /**
     * @param  array<object>  $events
     */
    public function __construct(
        public GameId $gameId,
        public GameName $name,
        public MessageMode $messageMode,
        public AdjudicationTiming $adjudicationTiming,
        public GameStartTiming $gameStartTiming,
        public bool $randomPowerAssignments,
        public Variant $variant,
        public PowerCollection $powerCollection,
        public PhasesInfo $phasesInfo,
        array $events = [],
    ) {
        $this->events = $events;
    }

    public static function createWithRandomAssignments(
        GameName $name,
        MessageMode $messageMode,
        AdjudicationTiming $adjudicationTiming,
        GameStartTiming $gameStartTiming,
        Variant $variant,
        PlayerId $playerId,
    ): self {

        $powers = PowerCollection::createFromVariantPowerCollection(
            $variant->powers
        );
        $powers->assignRandomly($playerId);

        $gameId = GameId::generate();

        return new self(
            $gameId,
            $name,
            $messageMode,
            $adjudicationTiming,
            $gameStartTiming,
            true,
            $variant,
            $powers,
            PhasesInfo::initialize(),
            [new GameCreatedEvent()]
        );
    }

    /**
     * @param  Option<VariantPowerId>  $variantPowerId
     *
     * @throws DomainException
     */
    public function join(PlayerId $playerId, Option $variantPowerId, CarbonImmutable $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot join game {$this->gameId}",
            $this->canJoin($playerId, $variantPowerId)
        );

        if ($this->randomPowerAssignments) {
            $this->powerCollection->assignRandomly($playerId);
        } else {
            $this->powerCollection->assign($playerId, $variantPowerId->get());
        }

        $this->pushEvent(new PlayerJoinedEvent());

        $this->startGameIfConditionsAreFulfilled($currentTime);
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

        $this->powerCollection->unassign($playerId);
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
        if (! $this->phasesInfo->currentPhase->get()->hasWinners()) {
            return false;
        }

        return true;
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
                $this->gameStartTiming->startWhenReady
                || $this->gameStartTiming->joinLengthExceeded($currentTime),
            ),
        );
    }

    public function startGameIfConditionsAreFulfilled(CarbonImmutable $currentTime): void
    {
        if ($this->canBeStarted($currentTime)->passes()) {
            $this->pushEvent(new GameStartedEvent());
        }
    }

    public function submitOrders(PlayerId $playerId, OrderCollection $orderCollection, bool $markAsReady, CarbonImmutable $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot submit orders for game {$this->gameId}",
            $this->canSubmitOrders($playerId, $currentTime)
        );

        $powerId = $this->powerCollection->getPowerIdByPlayerId($playerId);

        $this->phasesInfo->currentPhase->get()->orders->setOrdersForPower(
            $powerId,
            $orderCollection,
            $markAsReady
        );

        $this->pushEvent(new OrdersSubmittedEvent());

        $this->adjudicateGameIfConditionsAreFulfilled($currentTime);
    }

    public function markOrderStatus(PlayerId $playerId, bool $orderStatus, CarbonImmutable $currentTime): void
    {
        RulesetHandler::throwConditionally(
            "Player $playerId cannot mark order status for game {$this->gameId}",
            $this->canMarkOrderStatus($playerId, $currentTime)
        );

        $powerId = $this->powerCollection->getPowerIdByPlayerId($playerId);

        $this->phasesInfo->currentPhase->get()->markOrderStatus($powerId, $orderStatus);

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
                    ! $this->phasesInfo->currentPhase->get()->needsOrders(
                        $this->powerCollection->getPowerIdByPlayerId($playerId)
                    ),
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
                    ! $this->phasesInfo->currentPhase->get()->needsOrders(
                        $this->powerCollection->getPowerIdByPlayerId($playerId)
                    ),
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
                ! $this->phasesInfo->currentPhase->get()->allOrdersMarkedAsReady()
                && ! $this->phasesInfo->currentPhase->get()->adjudicationTimeExpired($currentTime),
            )
        );
    }
}
