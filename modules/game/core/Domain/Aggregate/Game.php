<?php

namespace Dnw\Game\Core\Domain\Aggregate;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Event\AggregateEventTrait;
use Dnw\Game\Core\Domain\Collection\PowerCollection;
use Dnw\Game\Core\Domain\Entity\MessageMode;
use Dnw\Game\Core\Domain\Entity\Variant;
use Dnw\Game\Core\Domain\Event\GameStartedEvent;
use Dnw\Game\Core\Domain\Exception\DomainException;
use Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\ValueObject\Phases\PhasesInfo;
use Dnw\Game\Core\Domain\ValueObject\PlayerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerId;
use PhpOption\Option;

class Game
{
    use AggregateEventTrait;

    public function __construct(
        public GameId $gameId,
        public GameName $name,
        public MessageMode $messageMode,
        public AdjudicationTiming $adjudicationTiming,
        public GameStartTiming $gameStartTiming,
        public bool $randomPowerAssignments,
        public Variant $variant,
        public PowerCollection $powers,
        public PhasesInfo $phasesInfo,
    ) {

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
        );
    }

    /**
     * @param  Option<VariantPowerId>  $variantPowerId
     *
     * @throws DomainException
     */
    public function join(PlayerId $playerId, Option $variantPowerId): void
    {
        if (! $this->canJoin($playerId, $variantPowerId)) {
            throw new DomainException("Player $playerId cannot join game {$this->gameId}");
        }
        if ($this->randomPowerAssignments) {
            $this->powers->assignRandomly($playerId);
        } else {
            $this->powers->assign($playerId, $variantPowerId->get());
        }

    }

    /**
     * @param  Option<VariantPowerId>  $variantPowerId
     */
    public function canJoin(PlayerId $playerId, Option $variantPowerId): bool
    {
        if ($this->hasBeenStarted()) {
            return false;
        }

        // Ensure that the initial phase has already been created
        if ($this->phasesInfo->count->int() === 1) {
            return false;
        }

        if (! $this->powers->hasAvailablePowers()) {
            return false;
        }

        if ($this->powers->containsPlayer($playerId)) {
            return false;
        }

        if (! $this->randomPowerAssignments
            && $this->powers->hasPowerFilled($variantPowerId->get())
        ) {
            return false;
        }

        return true;
    }

    /**
     * @throws DomainException
     */
    public function leave(PlayerId $playerId): void
    {
        if ($this->canLeave($playerId)) {
            $this->powers->unassign($playerId);
        }
        throw new DomainException("Player $playerId cannot leave game {$this->gameId}");
    }

    public function canLeave(PlayerId $playerId): bool
    {
        if ($this->hasBeenStarted()) {
            return false;
        }
        if (! $this->powers->containsPlayer($playerId)) {
            return false;
        }

        return true;
    }

    private function hasBeenStarted(): bool
    {
        return $this->phasesInfo->hasBeenStarted();
    }

    public function canBeStarted(CarbonImmutable $currentTime): bool
    {
        if (! $this->powers->hasAvailablePowers()) {
            return false;
        }

        if ($this->gameStartTiming->startWhenReady) {
            return true;
        }

        return $this->gameStartTiming->joinLengthExceeded($currentTime);
    }

    public function startGameIfConditionsAreFulfilled(CarbonImmutable $currentTime): void
    {
        if ($this->canBeStarted($currentTime)) {
            $this->pushEvent(new GameStartedEvent());
        }
    }
}
