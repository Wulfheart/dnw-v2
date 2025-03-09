<?php

namespace Dnw\Game\Application\Command\AdjudicateGame;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\Order as AdjudicatorOrder;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Dto\AdjudicationPowerDataDto;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseName;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Psr\Log\LoggerInterface;

readonly class AdjudicateGameCommandHandler
{
    public function __construct(
        private AdjudicatorService $adjudicatorService,
        private GameRepositoryInterface $gameRepository,
        private VariantRepositoryInterface $variantRepository,
        private PhaseRepositoryInterface $phaseRepository,
        private TimeProviderInterface $timeProvider,
        private LoggerInterface $logger,
    ) {}

    public function handle(AdjudicateGameCommand $command): AdjudicateGameCommandResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromId($command->gameId));
        if ($gameResult->hasErr()) {
            $this->logger->info('Game not found', ['gameId' => $command->gameId]);

            return AdjudicateGameCommandResult::err(AdjudicateGameCommandResult::E_GAME_NOT_FOUND);
        }
        $game = $gameResult->unwrap();
        $variantResult = $this->variantRepository->load($game->variant->id);
        if ($variantResult->hasErr()) {
            $this->logger->info('Variant not found', ['variantId' => $game->variant->id]);

            return AdjudicateGameCommandResult::err(AdjudicateGameCommandResult::E_VARIANT_NOT_FOUND);
        }
        $variant = $variantResult->unwrap();

        $encodedState = $this->phaseRepository->loadEncodedState($game->phasesInfo->currentPhase->unwrap()->phaseId);

        $orders = [];
        foreach ($game->powerCollection as $power) {
            $powerApiName = $variant->variantPowerCollection->getByVariantPowerId($power->variantPowerId)->id;

            $orders[] = new AdjudicatorOrder(
                $powerApiName,
                $power->currentPhaseData->unwrap()->orderCollection->mapOr(
                    fn (OrderCollection $orderCollection) => $orderCollection->toStringArray(),
                    []
                )
            );
        }

        // Todo: Error Handling
        $adjudicateGameRequest = new AdjudicateGameRequest(
            $encodedState->unwrap(),
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

            $powerApiName = $variant->variantPowerCollection->getByVariantPowerId($power->variantPowerId)->id;

            $phasePowerData = $adjudicationGameResult->getPhasePowerDataByPowerName($powerApiName);
            $possibleOrders = $adjudicationGameResult->getPossibleOrdersByPowerName($powerApiName);

            $newPhaseData = new NewPhaseData(
                count($possibleOrders->units) > 0,
                $adjudicationGameResult->powerHasWon($powerApiName),
                Count::fromInt($phasePowerData->supply_center_count),
                Count::fromInt($phasePowerData->unit_count),
            );

            $appliedOrdersResult = $adjudicationGameResult->getAppliedOrdersByPowerName($powerApiName);

            $appliedOrders = OrderCollection::fromStringArray($appliedOrdersResult);

            $adjudicationPowerData = new AdjudicationPowerDataDto(
                $power->powerId,
                $newPhaseData,
                $appliedOrders
            );

            $adjudicationPowerDataCollection->push($adjudicationPowerData);
        }

        $phaseName = PhaseName::fromString($adjudicationGameResult->phase_long);

        $game->applyAdjudication(
            $phaseType,
            $phaseName,
            $adjudicationPowerDataCollection,
            $this->timeProvider->getCurrentTime()
        );

        $this->phaseRepository->saveEncodedState(
            $game->phasesInfo->currentPhase->unwrap()->phaseId,
            $adjudicationGameResult->current_state_encoded
        );

        $this->phaseRepository->saveSvgWithOrders(
            $game->phasesInfo->lastPhaseId->unwrap(),
            $adjudicationGameResult->svg_with_orders
        );

        $this->phaseRepository->saveAdjudicatedSvg(
            $game->phasesInfo->currentPhase->unwrap()->phaseId,
            $adjudicationGameResult->svg_adjudicated
        );

        $this->gameRepository->save($game);

        return AdjudicateGameCommandResult::ok();

    }
}
