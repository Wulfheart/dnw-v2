<?php

namespace Dnw\Game\Core\Application\Command\InitialGameAdjudication;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Core\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;
use Psr\Log\LoggerInterface;

readonly class InitialGameAdjudicationCommandHandler
{
    public function __construct(
        private AdjudicatorService $adjudicatorService,
        private GameRepositoryInterface $gameRepository,
        private PhaseRepositoryInterface $phaseRepository,
        private VariantRepositoryInterface $variantRepository,
        private TimeProviderInterface $timeProvider,
        private LoggerInterface $logger,
    ) {}

    public function handle(InitialGameAdjudicationCommand $command): InitialGameAdjudicationResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromId($command->gameId));
        if ($gameResult->hasErr()) {
            $this->logger->info('Game not found', ['gameId' => $command->gameId]);

            return InitialGameAdjudicationResult::err(InitialGameAdjudicationResult::E_GAME_NOT_FOUND);
        }
        $game = $gameResult->unwrap();

        $variantResult = $this->variantRepository->load($game->variant->id);
        if ($variantResult->hasErr()) {
            $this->logger->info('Variant not found', ['variantId' => $game->variant->id]);

            return InitialGameAdjudicationResult::err(InitialGameAdjudicationResult::E_VARIANT_NOT_FOUND);
        }
        $variant = $variantResult->unwrap();

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
                new NewPhaseData(
                    count($possibleOrders->units) > 0,
                    $adjudicationGameResult->powerHasWon($phasePowerData->power),
                    Count::fromInt($phasePowerData->supply_center_count),
                    Count::fromInt($phasePowerData->unit_count),
                ),
            ));
        }

        $game->applyInitialAdjudication(
            $phaseType,
            $phasePowerCollection,
            $this->timeProvider->getCurrentTime()
        );

        $this->phaseRepository->saveEncodedState(
            $game->phasesInfo->currentPhase->unwrap()->phaseId,
            $adjudicationGameResult->current_state_encoded
        );

        $this->phaseRepository->saveAdjudicatedSvg(
            $game->phasesInfo->currentPhase->unwrap()->phaseId,
            $adjudicationGameResult->svg_adjudicated
        );

        $this->gameRepository->save($game);

        return InitialGameAdjudicationResult::ok();
    }
}
