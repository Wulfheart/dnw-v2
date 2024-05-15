<?php

namespace Dnw\Game\Core\Application\Command\InitialGameAdjudication;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Adapter\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;
use PhpOption\None;

readonly class InitialGameAdjudicationCommandHandler
{
    public function __construct(
        private AdjudicatorService $adjudicatorService,
        private GameRepositoryInterface $gameRepository,
        private PhaseRepositoryInterface $phaseRepository,
        private VariantRepositoryInterface $variantRepository,
        private TimeProviderInterface $timeProvider,
    ) {
    }

    public function handle(InitialGameAdjudicationCommand $command): void
    {
        $game = $this->gameRepository->load(GameId::fromId($command->gameId));
        $variant = $this->variantRepository->load($game->variant->id);

        $adjudicationGameResult = $this->adjudicatorService->initializeGame(
            $variant->apiName,
        );

        $phaseType = PhaseTypeEnum::from($adjudicationGameResult->phase_type);

        /** @var Collection<InitialAdjudicationPowerDataDto> $phasePowerCollection */
        $phasePowerCollection = new ArrayCollection();

        foreach ($adjudicationGameResult->phase_power_data as $phasePowerData) {
            $variantPower = $variant->variantPowerCollection->getByPowerApiName(VariantPowerApiName::fromString($phasePowerData->power));
            $powerId = $game->powerCollection->getByVariantPowerId($variantPower->id)->powerId;

            $possibleOrders = $adjudicationGameResult->getPossibleOrdersByPowerName($phasePowerData->power);

            $phasePowerCollection->push(new InitialAdjudicationPowerDataDto(
                $powerId,
                new PhasePowerData(
                    count($possibleOrders->units) > 0,
                    false,
                    $adjudicationGameResult->powerHasWon($phasePowerData->power),
                    Count::fromInt($phasePowerData->supply_center_count),
                    Count::fromInt($phasePowerData->unit_count),
                    None::create(),
                    None::create()
                ),
            ));
        }

        $game->applyInitialAdjudication(
            $phaseType,
            $phasePowerCollection,
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
