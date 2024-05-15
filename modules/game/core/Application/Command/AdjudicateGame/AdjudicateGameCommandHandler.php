<?php

namespace Dnw\Game\Core\Application\Command\AdjudicateGame;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\Order as AdjudicatorOrder;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Adapter\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Dto\AdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Exception;
use PhpOption\None;

readonly class AdjudicateGameCommandHandler
{
    public function __construct(
        private AdjudicatorService $adjudicatorService,
        private GameRepositoryInterface $gameRepository,
        private VariantRepositoryInterface $variantRepository,
        private PhaseRepositoryInterface $phaseRepository,
        private TimeProviderInterface $timeProvider,
    ) {
    }

    public function handle(AdjudicateGameCommand $command): void
    {
        $game = $this->gameRepository->load(GameId::fromId($command->gameId));
        $variant = $this->variantRepository->load($game->variant->id);

        if ($game->canAdjudicate($this->timeProvider->getCurrentTime())->fails()) {
            // TODO: Make a more specific exception
            throw new Exception();
        }

        $encodedState = $this->phaseRepository->loadEncodedState($game->phasesInfo->currentPhase->get()->phaseId);

        $orders = [];
        foreach ($game->powerCollection as $power) {
            $powerApiName = $variant->variantPowerCollection->getByVariantPowerId($power->variantPowerId)->apiName;

            $orders[] = new AdjudicatorOrder(
                $powerApiName,
                $power->currentPhaseData->get()->orderCollection->get()->toStringArray()
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

        /** @var Collection<AdjudicationPowerDataDto> $adjudicationPowerDataCollection */
        $adjudicationPowerDataCollection = new ArrayCollection();

        foreach ($game->powerCollection as $power) {

            $powerApiName = $variant->variantPowerCollection->getByVariantPowerId($power->variantPowerId)->apiName;

            $phasePowerData = $adjudicationGameResult->getPhasePowerDataByPowerName($powerApiName);
            $possibleOrders = $adjudicationGameResult->getPossibleOrdersByPowerName($powerApiName);

            $newPhaseData = new PhasePowerData(
                count($possibleOrders->units) > 0,
                false,
                $adjudicationGameResult->powerHasWon($powerApiName),
                Count::fromInt($phasePowerData->supply_center_count),
                Count::fromInt($phasePowerData->unit_count),
                None::create(),
                None::create(),
            );

            $appliedOrdersResult = $adjudicationGameResult->getAppliedOrdersByPowerName($powerApiName);

            $appliedOrders = OrderCollection::fromStringArray($appliedOrdersResult->orders);

            $adjudicationPowerData = new AdjudicationPowerDataDto(
                $power->powerId,
                $newPhaseData,
                $appliedOrders
            );

            $adjudicationPowerDataCollection->push($adjudicationPowerData);
        }

        $game->applyAdjudication(
            $phaseType,
            $adjudicationPowerDataCollection,
            $this->timeProvider->getCurrentTime()
        );

        $this->phaseRepository->saveEncodedState(
            $game->phasesInfo->currentPhase->get()->phaseId,
            $adjudicationGameResult->current_state_encoded
        );

        $this->phaseRepository->saveSvgWithOrders(
            $game->phasesInfo->lastPhaseId->get(),
            $adjudicationGameResult->svg_with_orders
        );

        $this->phaseRepository->saveAdjudicatedSvg(
            $game->phasesInfo->currentPhase->get()->phaseId,
            $adjudicationGameResult->svg_adjudicated
        );

        $this->gameRepository->save($game);

    }
}
