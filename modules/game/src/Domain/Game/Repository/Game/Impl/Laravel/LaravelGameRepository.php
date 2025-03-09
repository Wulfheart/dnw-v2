<?php

namespace Dnw\Game\Domain\Game\Repository\Game\Impl\Laravel;

use Dnw\Foundation\Aggregate\AggregateVersion;
use Dnw\Foundation\Aggregate\NewerAggregateVersionAvailableException;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Collection\PowerCollection;
use Dnw\Game\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Domain\Game\Entity\Phase;
use Dnw\Game\Domain\Game\Entity\Power;
use Dnw\Game\Domain\Game\Game;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Game\LoadGameResult;
use Dnw\Game\Domain\Game\Repository\Game\SaveGameResult;
use Dnw\Game\Domain\Game\Repository\Phase\Impl\Laravel\PhaseModel;
use Dnw\Game\Domain\Game\Repository\Phase\Impl\Laravel\PhasePowerDataModel;
use Dnw\Game\Domain\Game\StateMachine\GameStateMachine;
use Dnw\Game\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Domain\Game\ValueObject\AdjudicationTiming\NoAdjudicationWeekdayCollection;
use Dnw\Game\Domain\Game\ValueObject\AdjudicationTiming\PhaseLength;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseName;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Domain\Game\ValueObject\Phases\PhasesInfo;
use Dnw\Game\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Shared\VariantKey;
use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;
use Illuminate\Database\DatabaseManager;
use Wulfheart\Option\Option;

class LaravelGameRepository implements GameRepositoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private DatabaseManager $databaseManager
    ) {}

    public function load(GameId $gameId): LoadGameResult
    {
        $game = GameModel::with(
            'currentPhase.powerData',
            'powers',
            'lastPhase.powerData'
        )->find((string) $gameId);

        if ($game === null) {
            return LoadGameResult::err(LoadGameResult::E_GAME_NOT_FOUND);
        }

        $gameId = GameId::fromString($game->id);
        $gameName = GameName::fromString($game->name);
        $gameStateMachine = new GameStateMachine($game->current_state);
        $adjudicationTiming = new AdjudicationTiming(
            PhaseLength::fromMinutes($game->adjudication_timing_phase_length_in_minutes),
            NoAdjudicationWeekdayCollection::fromWeekdaysArray($game->adjudication_timing_no_adjudication_weekdays),
        );
        $gameStartTiming = new GameStartTiming(
            new DateTime($game->game_start_timing_start_of_join_phase),
            JoinLength::fromDays($game->game_start_timing_join_length_in_days),
            $game->game_start_timing_start_when_ready,
        );
        $arr = $game->variant_data_variant_power_ids->map(
            fn (string $variantPowerId) => VariantPowerKey::fromString($variantPowerId)
        );
        $variantData = new GameVariantData(
            VariantKey::fromString($game->variant_data_variant_key),
            VariantPowerIdCollection::build(
                ...$game->variant_data_variant_power_ids->map(
                    fn (string $variantPowerId) => VariantPowerKey::fromString($variantPowerId)
                )->toArray()
            ),
            Count::fromInt($game->variant_data_default_supply_centers_to_win_count),
        );

        $currentPhase = Option::fromNullable($game->currentPhase)->mapIntoOption(
            fn (PhaseModel $phase) => new Phase(
                PhaseId::fromString($phase->id),
                $phase->type,
                PhaseName::fromString($phase->name),
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
                VariantPowerKey::fromString($power->variant_power_key),
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

        return LoadGameResult::ok($loadedGame);

    }

    public function save(Game $game): SaveGameResult
    {
        $this->databaseManager->transaction(function () use ($game) {
            $this->saveOrUpdateGame($game);
            $this->savePhase($game);
            $this->savePowers($game);
        });

        $this->eventDispatcher->dispatchMultiple($game->releaseEvents());

        return SaveGameResult::ok();
    }

    private function saveOrUpdateGame(Game $game): void
    {
        $oldVersion = clone $game->version;
        $gameModel = new GameModel([
            'id' => (string) $game->gameId,
            'name' => (string) $game->name,
            'current_state' => $game->gameStateMachine->currentState(),
            'variant_data_variant_key' => (string) $game->variant->id,
            'variant_data_variant_power_ids' => $game->variant->variantPowerIdCollection->map(
                fn (VariantPowerKey $variantPowerId) => (string) $variantPowerId
            )->toArray(),
            'variant_data_default_supply_centers_to_win_count' => $game->variant->defaultSupplyCentersToWinCount->int(),
            'adjudication_timing_phase_length_in_minutes' => $game->adjudicationTiming->phaseLength->minutes(),
            'adjudication_timing_no_adjudication_weekdays' => $game->adjudicationTiming->noAdjudicationWeekdays->toArray(),
            'random_power_assignments' => $game->randomPowerAssignments,
            'game_start_timing_start_of_join_phase' => $game->gameStartTiming->startOfJoinPhase->toDateTimeString(),
            'game_start_timing_join_length_in_days' => $game->gameStartTiming->joinLength->toDays(),
            'game_start_timing_start_when_ready' => $game->gameStartTiming->startWhenReady,
        ]);

        if ($oldVersion->isInitial()) {
            $gameModel->version = $game->version->increment()->int();
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

            PhaseModel::updateOrCreate(
                [
                    'id' => (string) $phase->phaseId,
                ],
                [
                    'id' => (string) $phase->phaseId,
                    'name' => (string) $phase->phaseName,
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
                    'variant_power_key' => (string) $power->variantPowerId,
                    'player_id' => $power->playerId->mapOr(fn (PlayerId $playerId) => (string) $playerId, null),
                ]
            );

            if ($power->currentPhaseData->isSome()) {
                $phasePowerData = $power->currentPhaseData->unwrap();

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

    public function getGameIdByName(GameName $name): Option
    {
        $game = GameModel::where('name', (string) $name)->first('id');

        if ($game === null) {
            return Option::none();
        }

        return Option::some(GameId::fromString($game->id));
    }
}
