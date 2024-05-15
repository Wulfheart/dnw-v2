<?php

namespace Dnw\Game\Core\Application\Command\InitialGameAdjudication;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Adjudicator\Dto\PossibleOrder;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Game\Core\Domain\Adapter\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Collection\PhasePowerCollection;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;
use PhpOption\None;

readonly class InitialGameAdjudicationCommandHandler
{
    public function __construct(
        private AdjudicatorService $adjudicatorService,
        private GameRepositoryInterface $gameRepository,
        private PhaseRepositoryInterface $phaseRepository,
        private TimeProviderInterface $timeProvider,
    ) {
    }

    public function handle(InitialGameAdjudicationCommand $command): void
    {
        $game = $this->gameRepository->load(GameId::fromId($command->gameId));

        $adjudicationGameResult = $this->adjudicatorService->initializeGame(
            $game->variant->apiName,
        );

        $phaseType = PhaseTypeEnum::from($adjudicationGameResult->phase_type);

        $apiNameToPowerId = function (string $name) use ($game): PowerId {
            $variantPower = $game->variant->variantPowerCollection->getByPowerApiName(VariantPowerApiName::fromString($name));

            return $game->powerCollection->getByVariantPowerId($variantPower->id)->powerId;
        };

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

        $game->applyInitialAdjudication(
            $phaseType,
            $phasePowerDataCollection,
            $this->timeProvider->getCurrentTime()
        );

        $this->phaseRepository->saveEncodedState(
            $game->phasesInfo->currentPhase->get()->phaseId,
            $adjudicationGameResult->current_state_encoded
        );

        $this->phaseRepository->saveAdjudicatedSvg(
            $game->phasesInfo->currentPhase->get()->phaseId,
            $adjudicationGameResult->svg_adjudicated
        );

        $this->gameRepository->save($game);
    }
}
