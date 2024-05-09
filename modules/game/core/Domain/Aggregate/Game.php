<?php

use Collection\PowerCollection;
use Entity\MessageMode;
use Entity\Variant;
use PhpOption\None;
use PhpOption\Option;
use ValueObjects\AdjudicationTiming\AdjudicationTiming;
use ValueObjects\GameName;
use ValueObjects\GameStartTiming\GameStartTiming;
use ValueObjects\Phases\PhasesInfo;
use ValueObjects\PlayerId;

final class Game
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
