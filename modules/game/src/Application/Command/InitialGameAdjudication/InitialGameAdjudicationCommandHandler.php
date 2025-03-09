<?php

namespace Dnw\Game\Application\Command\InitialGameAdjudication;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Domain\Game\Dto\InitialAdjudicationPowerDataDto;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Phase\NewPhaseData;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseName;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
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

    public function handle(InitialGameAdjudicationCommand $command): InitialGameAdjudicationCommandResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromId($command->gameId));
        if ($gameResult->hasErr()) {
            $this->logger->info('Game not found', ['gameId' => $command->gameId]);

            return InitialGameAdjudicationCommandResult::err(InitialGameAdjudicationCommandResult::E_GAME_NOT_FOUND);
        }
        $game = $gameResult->unwrap();

        $variantResult = $this->variantRepository->load($game->variant->id);
        if ($variantResult->hasErr()) {
            $this->logger->info('Variant not found', ['variantId' => $game->variant->id]);

            return InitialGameAdjudicationCommandResult::err(InitialGameAdjudicationCommandResult::E_VARIANT_NOT_FOUND);
        }
        $variant = $variantResult->unwrap();

        $adjudicationGameResult = $this->adjudicatorService->initializeGame(
            $variant->id,
        );

        $phaseType = PhaseTypeEnum::from($adjudicationGameResult->phase_type);
        $phaseName = PhaseName::fromString($adjudicationGameResult->phase_long);

        /** @var Collection<InitialAdjudicationPowerDataDto> $phasePowerCollection */
        $phasePowerCollection = new ArrayCollection();

        foreach ($adjudicationGameResult->phase_power_data as $phasePowerData) {
            $variantPower = $variant->variantPowerCollection->getByVariantPowerId(
                VariantPowerId::fromString($phasePowerData->power)
            );
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
            $phaseName,
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

        return InitialGameAdjudicationCommandResult::ok();
    }
}
