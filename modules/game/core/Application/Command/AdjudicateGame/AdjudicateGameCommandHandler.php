<?php

namespace Dnw\Game\Core\Application\Command\AdjudicateGame;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\Order as AdjudicatorOrder;
use Dnw\Adjudicator\Dto\PossibleOrder;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Game\Core\Domain\Adapter\TimeProviderInterface;
use Dnw\Game\Core\Domain\Collection\AppliedOrdersCollection;
use Dnw\Game\Core\Domain\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\Collection\WinnerCollection;
use Dnw\Game\Core\Domain\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\ValueObject\Count;
use Dnw\Game\Core\Domain\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\ValueObject\Order\Order;
use Dnw\Game\Core\Domain\ValueObject\Phase\AppliedOrders;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerApiName;
use PhpOption\None;
use PhpOption\Some;

readonly class AdjudicateGameCommandHandler
{
    public function __construct(
        private AdjudicatorService $adjudicatorService,
        private GameRepositoryInterface $gameRepository,
        private PhaseRepositoryInterface $phaseRepository,
        private TimeProviderInterface $timeProvider,
    ) {
    }

    public function handle(AdjudicateGameCommand $command): void
    {
        $game = $this->gameRepository->load(GameId::fromId($command->gameId));

        $encodedState = $this->phaseRepository->loadEncodedState($game->phasesInfo->currentPhase->get()->phaseId);

        $orders = [];
        foreach ($game->phasesInfo->currentPhase->get()->phasePowerCollection as $phasePowerData) {
            $powerName = $game->variant->variantPowerCollection->getByVariantPowerId(
                $game->powerCollection->getByPowerId($phasePowerData->powerId)->variantPowerId
            )->powerApiName;

            $orders[] = new AdjudicatorOrder(
                $powerName,
                $phasePowerData->orderCollection->getOrElse(null)?->toStringArray() ?? []
            );
        }

        $adjudicateGameRequest = new AdjudicateGameRequest(
            $encodedState,
            $orders,
            $game->calculateSupplyCenterCountForWinning()->int()
        );

        $adjudicationGameResult = $this->adjudicatorService->adjudicateGame(
            $adjudicateGameRequest
        );

        $phaseType = PhaseTypeEnum::from($adjudicationGameResult->phase_type);

        $apiNameToPowerId = function (string $name) use ($game): PowerId {
            $variantPower = $game->variant->variantPowerCollection->getByPowerApiName(VariantPowerApiName::fromString($name));

            return $game->powerCollection->getByVariantPowerId($variantPower->id)->powerId;
        };

        $appliedOrderCollection = new AppliedOrdersCollection();
        foreach ($adjudicationGameResult->applied_orders as $appliedOrder) {
            $appliedOrders = array_map(
                fn (string $order) => Order::fromString($order),
                $appliedOrder->orders
            );
            $orderCollection = new OrderCollection($appliedOrders);

            $powerId = $apiNameToPowerId($appliedOrder->power);

            $appliedOrderCollection->push(new AppliedOrders($powerId, $orderCollection));
        }

        $phasePowerDataCollection = new PhasePowerCollection();
        foreach ($adjudicationGameResult->phase_power_data as $phasePowerData) {
            /** @var PossibleOrder $possibleOrders */
            $possibleOrders = ArrayCollection::fromArray($adjudicationGameResult->possible_orders)->findBy(
                fn ($possibleOrder) => $possibleOrder->power === $phasePowerData->power
            )->get();

            $ordersNeeded = count($possibleOrders->units) > 0;

            $powerId = $apiNameToPowerId($phasePowerData->power);

            $ppd = new PhasePowerData(
                $powerId,
                $ordersNeeded,
                false,
                Count::fromInt($phasePowerData->supply_center_count),
                Count::fromInt($phasePowerData->unit_count),
                None::create(),
                None::create(),
            );

            $phasePowerDataCollection->push($ppd);
        }

        $winnerCollection = new WinnerCollection(
            array_map(fn (string $winner) => $apiNameToPowerId($winner), $adjudicationGameResult->winners)
        );

        $winnerCollection = $winnerCollection->count() > 0 ? Some::create($winnerCollection) : None::create();

        $game->applyAdjudication(
            $phaseType,
            $phasePowerDataCollection,
            $winnerCollection,
            $appliedOrderCollection,
            $this->timeProvider->getCurrentTime()
        );

        $this->phaseRepository->saveEncodedState(
            $game->phasesInfo->currentPhase->get()->phaseId,
            $adjudicationGameResult->current_state_encoded
        );

        $this->phaseRepository->saveSvgWithOrders(
            $game->phasesInfo->previousPhase->get()->phaseId,
            $adjudicationGameResult->svg_with_orders
        );

        $this->phaseRepository->saveAdjudicatedSvg(
            $game->phasesInfo->currentPhase->get()->phaseId,
            $adjudicationGameResult->svg_adjudicated
        );

        $this->gameRepository->save($game);

    }
}
