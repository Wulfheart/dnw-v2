<?php

namespace Dnw\Game\Tests\Factory;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\NoAdjudicationWeekdayCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\PhaseLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Core\Infrastructure\Adapter\RandomNumberGenerator;

class GameBuilder
{
    private function __construct(
        private Game $game
    ) {

    }

    public static function create(): self
    {
        $standardVariant = VariantFactory::standard();

        $game = Game::create(
            GameId::generate(),
            GameName::fromString('Test Game'),
            new AdjudicationTiming(
                PhaseLength::fromMinutes(240),
                NoAdjudicationWeekdayCollection::fromWeekdaysArray([1, 2, 3, 4, 5]),
            ),
            new GameStartTiming(
                CarbonImmutable::now(),
                JoinLength::fromDays(2),
                true
            ),
            new GameVariantData(
                $standardVariant->id,
                VariantPowerIdCollection::fromCollection(
                    $standardVariant->variantPowerCollection->map(fn (VariantPower $variantPower) => $variantPower->id)
                ),
                $standardVariant->defaultSupplyCentersToWinCount,
            ),
            PlayerId::generate(),
            (new RandomNumberGenerator())->generate(...),
        );

        return new self($game);
    }

    public function join(): self
    {
        return $this;
    }

    public function makeFull(): self
    {
        return $this;
    }

    public function build(): Game
    {
        return $this->game;
    }
}
