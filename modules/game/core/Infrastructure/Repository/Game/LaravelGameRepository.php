<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Game;

use Dnw\Foundation\Aggregate\AggregateVersion;
use Dnw\Foundation\Aggregate\NewerAggregateVersionAvailableException;
use Dnw\Foundation\DateTime\DateTime;
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
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Core\Infrastructure\Model\Game\GameModel;
use Dnw\Game\Core\Infrastructure\Model\Game\PhaseModel;
use Dnw\Game\Core\Infrastructure\Model\Game\PhasePowerDataModel;
use Dnw\Game\Core\Infrastructure\Model\Game\PowerModel;
use Illuminate\Database\DatabaseManager;
use Std\Option;

class LaravelGameRepository implements GameRepositoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private DatabaseManager $databaseManager
    ) {}

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
            new DateTime($game->game_start_timing_start_of_join_phase),
            JoinLength::fromDays($game->game_start_timing_join_length),
            $game->game_start_timing_start_when_ready,
        );
        $arr = $game->variant_data_variant_power_ids->map(
            fn (string $variantPowerId) => VariantPowerId::fromString($variantPowerId)
        );
        $variantData = new GameVariantData(
            VariantId::fromString($game->variant_data_variant_id),
            VariantPowerIdCollection::build(
                ...$game->variant_data_variant_power_ids->map(
                    fn (string $variantPowerId) => VariantPowerId::fromString($variantPowerId)
                )->toArray()
            ),
            Count::fromInt($game->variant_data_default_supply_centers_to_win_count),
        );

        $currentPhase = Option::fromNullable($game->currentPhase)->mapIntoOption(
            fn (PhaseModel $phase) => new Phase(
                PhaseId::fromString($phase->id),
                PhaseTypeEnum::from($phase->type),
                Option::fromNullable($phase->adjudication_time)->mapIntoOption(fn (string $adjudicationTime) => new DateTime($adjudicationTime)),
            ),
        );

        $lastPhaseId = PhaseId::fromNullableString($game->lastPhase?->id);

        $phasesInfo = new PhasesInfo(
            Count::fromInt($game->phases->count()),
            $currentPhase,
            $lastPhaseId
        );

        $powerCollection = new PowerCollection();
        foreach ($game->powers as $power) {
            $power = new Power(
                PowerId::fromString($power->id),
                Option::fromNullable($power->player_id)->mapIntoOption(
                    fn (string $playerId) => PlayerId::fromString($playerId)
                ),
                VariantPowerId::fromString($power->variant_power_id),
                Option::fromNullable($game->currentPhase)->mapIntoOption(function (PhaseModel $phase) use ($power) {
                    /** @var PhasePowerDataModel $powerData */
                    $powerData = $phase->powerData->where('power_id', $power->id)->firstOrFail();

                    return new PhasePowerData(
                        $powerData->orders_needed,
                        $powerData->marked_as_ready,
                        $powerData->is_winner,
                        Count::fromInt($powerData->supply_center_count),
                        Count::fromInt($powerData->unit_count),
                        Option::fromNullable($powerData->order_collection)->mapIntoOption(
                            fn (array $orders) => OrderCollection::fromStringArray($orders)
                        )
                    );
                }),
                Option::fromNullable($game->lastPhase?->powerData->where('power_id', $power->id)->firstOrFail()->applied_orders)
                    ->mapIntoOption(fn (array $orders) => OrderCollection::fromStringArray($orders))
            );

            $powerCollection->push($power);
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
            new AggregateVersion($game->version),
            [],
        );

        return $loadedGame;
    }

    public function save(Game $game): void
    {
        $this->databaseManager->transaction(function () use ($game) {
            $this->saveOrUpdateGame($game);
            $this->savePhase($game);
            $this->savePowers($game);
        });

        $this->eventDispatcher->dispatchMultiple($game->releaseEvents());
    }

    private function saveOrUpdateGame(Game $game): void
    {
        $oldVersion = clone $game->version;
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

        if ($oldVersion->isInitial()) {
            $gameModel->version = $game->version->int();
            $gameModel->save();
        } else {
            $game->version = $oldVersion->increment();
            $gameModel->version = $game->version->int();
            $updateCount = GameModel::where('id', (string) $game->gameId)->where('version', $oldVersion->int())->update($gameModel->attributesToArray());
            if ($updateCount === 0) {
                throw new NewerAggregateVersionAvailableException();
            }
        }
    }

    private function savePhase(Game $game): void
    {
        if ($game->phasesInfo->currentPhase->isSome()) {
            $phase = $game->phasesInfo->currentPhase->unwrap();
            $phaseModel = new PhaseModel([
                'id' => (string) $phase->phaseId,
                'game_id' => (string) $game->gameId,
                'type' => $phase->phaseType->value,
                'adjudication_time' => $phase->adjudicationTime->mapOr(fn (DateTime $adjudicationTime) => $adjudicationTime->toDateTimeString(), null),
                'ordinal_number' => $game->phasesInfo->count->int(),
            ]);

            PhaseModel::updateOrCreate(
                [
                    'id' => (string) $phase->phaseId,
                ],
                [
                    'id' => (string) $phase->phaseId,
                    'game_id' => (string) $game->gameId,
                    'type' => $phase->phaseType->value,
                    'adjudication_time' => $phase->adjudicationTime->mapOr(fn (DateTime $adjudicationTime) => $adjudicationTime->toDateTimeString(), null),
                    'ordinal_number' => $game->phasesInfo->count->int(),
                ]
            );
        }
    }

    private function savePowers(Game $game): void
    {
        foreach ($game->powerCollection as $power) {
            PowerModel::updateOrCreate(
                [
                    'id' => (string) $power->powerId,
                ],
                [
                    'game_id' => (string) $game->gameId,
                    'variant_power_id' => (string) $power->variantPowerId,
                    'player_id' => $power->playerId->mapOr(fn (PlayerId $playerId) => (string) $playerId, null),
                ]
            );

            if ($power->currentPhaseData->isSome()) {
                $phasePowerData = $power->currentPhaseData->unwrap();
                $phasePowerDataModel = new PhasePowerDataModel([
                    'phase_id' => (string) $game->phasesInfo->currentPhase->unwrap()->phaseId,
                    'power_id' => (string) $power->powerId,
                    'orders_needed' => $phasePowerData->ordersNeeded,
                    'marked_as_ready' => $phasePowerData->markedAsReady,
                    'is_winner' => $phasePowerData->isWinner,
                    'supply_center_count' => $phasePowerData->supplyCenterCount->int(),
                    'unit_count' => $phasePowerData->unitCount->int(),
                    'order_collection' => $phasePowerData->orderCollection->mapOr(fn (OrderCollection $orderCollection) => $orderCollection->toStringArray(), null),
                    'applied_orders' => null,
                ]);

                PhasePowerDataModel::updateOrCreate(
                    [
                        'power_id' => (string) $power->powerId,
                        'phase_id' => (string) $game->phasesInfo->currentPhase->unwrap()->phaseId,
                    ],
                    [
                        'orders_needed' => $phasePowerData->ordersNeeded,
                        'marked_as_ready' => $phasePowerData->markedAsReady,
                        'is_winner' => $phasePowerData->isWinner,
                        'supply_center_count' => $phasePowerData->supplyCenterCount->int(),
                        'unit_count' => $phasePowerData->unitCount->int(),
                        'order_collection' => $phasePowerData->orderCollection->mapOr(fn (OrderCollection $orderCollection) => $orderCollection->toStringArray(), null),
                        'applied_orders' => $phasePowerData->orderCollection->mapOr(fn (OrderCollection $orderCollection) => $orderCollection->toStringArray(), null),
                    ]
                );
            }

            if ($power->appliedOrders->isSome()) {
                $lastPhaseId = $game->phasesInfo->lastPhaseId->unwrap();
                PhasePowerDataModel::query()
                    ->where('power_id', (string) $power->powerId)
                    ->where('phase_id', (string) $lastPhaseId)
                    ->update([
                        'applied_orders' => $power->appliedOrders->mapOr(
                            fn (OrderCollection $orderCollection) => $orderCollection->toArray(),
                            null
                        ),
                    ]);
            }
        }
    }
}
