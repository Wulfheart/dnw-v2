<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

use Dnw\Game\Core\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\NoAdjudicationWeekdayCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\PhaseLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Psr\Log\LoggerInterface;

readonly class CreateGameCommandHandler
{
    public function __construct(
        private VariantRepositoryInterface $variantRepository,
        private TimeProviderInterface $timeProvider,
        private GameRepositoryInterface $gameRepository,
        private PlayerRepositoryInterface $playerRepository,
        private RandomNumberGeneratorInterface $randomNumberGenerator,
        private LoggerInterface $logger,
    ) {}

    public function handle(
        CreateGameCommand $command
    ): CreateGameResult {
        // TODO: Automatically append a number to the game name if it already exists

        $player = $this->playerRepository->load(PlayerId::fromString($command->creatorId));
        if ($player->canParticipateInAnotherGame()->fails()) {
            $this->logger->warning(
                'Player cannot participate in another game',
                ['playerId' => $command->creatorId]
            );

            return CreateGameResult::err(CreateGameResult::E_NOT_ALLOWED_TO_CREATE_GAME);
        }

        $adjudicationTiming = new AdjudicationTiming(
            PhaseLength::fromMinutes($command->phaseLengthInMinutes),
            NoAdjudicationWeekdayCollection::fromWeekdaysArray($command->weekdaysWithoutAdjudication)
        );

        $gameStartTiming = new GameStartTiming(
            $this->timeProvider->getCurrentTime(),
            JoinLength::fromDays($command->joinLengthInDays),
            $command->startWhenReady,
        );

        $variantResult = $this->variantRepository->load(VariantId::fromString($command->variantId));

        if ($variantResult->hasErr()) {
            $this->logger->warning(
                'Unable to load variant',
                ['variantId' => $command->variantId, 'error' => $variantResult->unwrapErr()]
            );

            return CreateGameResult::err(CreateGameResult::E_UNABLE_TO_LOAD_VARIANT);
        }

        $variant = $variantResult->unwrap();
        $variantData = new GameVariantData(
            $variant->id,
            VariantPowerIdCollection::fromCollection($variant->variantPowerCollection->map(
                fn ($variantPower) => $variantPower->id
            )),
            $variant->defaultSupplyCentersToWinCount
        );

        $game = Game::create(
            GameId::fromId($command->gameId),
            GameName::fromString($command->name),
            $adjudicationTiming,
            $gameStartTiming,
            $variantData,
            $command->randomPowerAssignments,
            PlayerId::fromString($command->creatorId),
            $command->selectedVariantPowerId->mapIntoOption(fn ($id) => VariantPowerId::fromString($id)),
            $this->randomNumberGenerator->generate(...)
        );

        $this->gameRepository->save($game);

        $this->logger->info('Game created', ['gameId' => $command->gameId, '']);

        return CreateGameResult::ok();
    }
}
