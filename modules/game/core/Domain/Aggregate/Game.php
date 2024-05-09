<?php

namespace Dnw\Game\Core\Domain\Aggregate;

use Dnw\Game\Core\Domain\Collection\PowerCollection;
use Dnw\Game\Core\Domain\Entity\MessageMode;
use Dnw\Game\Core\Domain\Entity\Variant;
use Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\ValueObject\GameName;
use Dnw\Game\Core\Domain\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\ValueObject\Phases\PhasesInfo;
use Dnw\Game\Core\Domain\ValueObject\PlayerId;
use PhpOption\None;
use PhpOption\Option;

class Game
{
    public function __construct(
        public GameName $name,
        public MessageMode $messageMode,
        public AdjudicationTiming $adjudicationTiming,
        public GameStartTiming $gameStartTiming,
        public bool $randomPowerAssignments,
        public Variant $variant,
        public PowerCollection $powers,
        public DateTimeImmutable $startTime,
        public PhasesInfo $phasesInfo,
        /** @var Option<DateTimeImmutable> $lockedAt */
        public Option $lockedAt,
    ) {

    }

    public static function createWithRandomAssignments(
        GameName $name,
        MessageMode $messageMode,
        AdjudicationTiming $adjudicationTiming,
        GameStartTiming $gameStartTiming,
        Variant $variant,
        PlayerId $playerId,
        DateTimeImmutable $startTime,
    ): self {

        $powers = PowerCollection::createFromVariantPowerCollection(
            $variant->powers
        );
        $powers->assignRandomly($playerId);

        return new self(
            $name,
            $messageMode,
            $adjudicationTiming,
            $gameStartTiming,
            true,
            $variant,
            $powers,
            $startTime,
            PhasesInfo::initialize(),
            None::create(),
        );
    }

    public function joinWithRandomAssignment(PlayerId $playerId): void
    {
        if ($this->powers->hasAvailablePowers()) {
            $this->powers->assignRandomly($playerId);
        }
    }

    public function lock(): void
    {
        $this->lockedAt = Option::fromValue(new DateTimeImmutable());
    }

    public function unlock(): void
    {
        $this->lockedAt = None::create();
    }
}
