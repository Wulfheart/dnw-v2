<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Game;

use Carbon\CarbonImmutable;
use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Collection\PowerCollection;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Entity\Phase;
use Dnw\Game\Core\Domain\Game\Entity\Power;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\StateMachine\GameStateMachine;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\NoAdjudicationWeekdayCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\PhaseLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\Game\ValueObject\Phases\PhasesInfo;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Core\Infrastructure\Model\Game\GameModel;
use Dnw\Game\Core\Infrastructure\Model\Game\PhaseModel;
use Dnw\Game\Core\Infrastructure\Model\Game\PhasePowerDataModel;
use Std\Option;

class LaravelGameRepository implements GameRepositoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function load(GameId $gameId): Game
    {
        $game = GameModel::with(
            'currentPhase.powerData',
            'powers',
            'lastPhase.powerData'
        )->findOr((string) $gameId, fn () => throw new NotFoundException());

        $gameId = GameId::fromString($game->id);
        $gameName = GameName::fromString($game->name);
        $gameStateMachine = new GameStateMachine($game->current_state);
        $adjudicationTiming = new AdjudicationTiming(
            PhaseLength::fromMinutes($game->adjudication_timing_phase_length),
            NoAdjudicationWeekdayCollection::fromWeekdaysArray($game->adjudication_timing_no_adjudication_weekdays),
        );
        $gameStartTiming = new GameStartTiming(
            new CarbonImmutable($game->game_start_timing_start_of_join_phase),
            JoinLength::fromDays($game->game_start_timing_join_length),
            $game->game_start_timing_start_when_ready,
        );
        $variantData = new GameVariantData(
            VariantId::fromString($game->variant_data_variant_id),
            VariantPowerIdCollection::build(
                $game->variant_data_variant_power_ids->map(
                    fn (string $variantPowerId) => VariantPowerId::fromString($variantPowerId)
                )->toArray()
            ),
            Count::fromInt($game->variant_data_variant_power_count),
        );

        $currentPhase = Option::fromValue($game->currentPhase)->map(
            fn (PhaseModel $phase) => new Phase(
                PhaseId::fromString($phase->id),
                PhaseTypeEnum::from($phase->type),
                // @phpstan-ignore-next-line
                Option::fromValue($phase->adjudication_time)->map(fn (string $adjudicationTime) => new CarbonImmutable($adjudicationTime)),
            ),
        );

        $lastPhaseId = PhaseId::fromNullableString($game->currentPhase?->id);

        $phasesInfo = new PhasesInfo(
            Count::fromInt($game->phases->count()),
            $currentPhase,
            $lastPhaseId
        );

        $powerCollection = new PowerCollection();
        foreach ($game->powers as $power) {
            $power = new Power(
                PowerId::fromString($power->id),
                Option::fromValue($power->player_id)->map(fn (string $playerId) => PlayerId::fromString($playerId)),
                VariantPowerId::fromString($power->variant_power_id),
                Option::fromValue($game->currentPhase)->map(function (PhaseModel $phase) use ($power) {
                    /** @var PhasePowerDataModel $powerData */
                    $powerData = $phase->powerData->where('power_id', $power->id)->firstOrFail();

                    return new PhasePowerData(
                        $powerData->orders_needed,
                        $powerData->marked_as_ready,
                        $powerData->is_winner,
                        Count::fromInt($powerData->supply_center_count),
                        Count::fromInt($powerData->unit_count),
                        // @phpstan-ignore-next-line
                        Option::fromValue($powerData->order_collection)->map(fn (array $orders) => OrderCollection::fromStringArray($orders))
                    );
                }),
                Option::fromValue($game->lastPhase?->powerData->where('power_id', $power->id)->firstOrFail()->applied_orders)->map(fn (array $orders) => OrderCollection::fromStringArray($orders))
            );
        }

        $loadedGame = new Game(
            $gameId,
            $gameName,
            $gameStateMachine,
            $adjudicationTiming,
            $gameStartTiming,
            $game->random_power_assignments,
            $variantData,
            $powerCollection,
            $phasesInfo,
            [],
        );

        return $loadedGame;
    }

    public function save(Game $game): void
    {
        $gameModel = new GameModel([
            'id' => (string) $game->gameId,
            'name' => (string) $game->name,
            'current_state' => $game->gameStateMachine->currentState(),
            'variant_data_variant_id' => (string) $game->variant->id,
            'variant_data_variant_power_ids' => $game->variant->variantPowerIdCollection->map(
                fn (VariantPowerId $variantPowerId) => (string) $variantPowerId
            )->toArray(),
            'variant_data_default_supply_centers_to_win_count' => $game->variant->defaultSupplyCentersToWinCount->int(),
            'adjudication_timing_phase_length' => $game->adjudicationTiming->phaseLength->minutes(),
            'adjudication_timing_no_adjudication_weekdays' => $game->adjudicationTiming->noAdjudicationWeekdays->toArray(),
            'random_power_assignments' => $game->randomPowerAssignments,
            'game_start_timing_start_of_join_phase' => $game->gameStartTiming->startOfJoinPhase->toDateTimeString(),
            'game_start_timing_join_length' => $game->gameStartTiming->joinLength->toDays(),
            'game_start_timing_start_when_ready' => $game->gameStartTiming->startWhenReady,
        ]);

        $gameModel->powers->add()





        $this->eventDispatcher->dispatchMultiple($game->releaseEvents());
    }
}
