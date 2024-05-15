<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

use Dnw\Game\Core\Domain\Adapter\RandomNumberGeneratorInterface;
use Dnw\Game\Core\Domain\Adapter\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Entity\MessageMode;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\MessageModeRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\NoAdjudicationWeekdayCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\PhaseLength;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Core\Domain\Game\ValueObject\MessageMode\MessageModeId;
use Dnw\Game\Core\Domain\Game\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantId;
use PhpOption\None;

readonly class CreateGameCommandHandler
{
    public function __construct(
        private MessageModeRepositoryInterface $messageModeRepository,
        private VariantRepositoryInterface $variantRepository,
        private TimeProviderInterface $timeProvider,
        private GameRepositoryInterface $gameRepository,
        private RandomNumberGeneratorInterface $randomNumberGenerator,
    ) {
    }

    public function handle(
        CreateGameCommand $command
    ): void {
        if ($command->customMessageModePermissions->isDefined()) {
            $customMessageModePermissions = $command->customMessageModePermissions->get();
            $messageMode = new MessageMode(
                None::create(),
                None::create(),
                true,
                $customMessageModePermissions->description,
                $command->isAnonymous,
                $customMessageModePermissions->allowOnlyPublicMessages,
                $customMessageModePermissions->allowCreationOfGroupChats,
                $customMessageModePermissions->allowAdjustmentMessages,
                $customMessageModePermissions->allowMoveMessages,
                $customMessageModePermissions->allowRetreatMessages,
                $customMessageModePermissions->allowPreGameMessages,
                $customMessageModePermissions->allowPostGameMessages,
            );
        } else {
            $messageMode = $this->messageModeRepository->load(
                MessageModeId::fromString($command->messageModeId->get())
            );
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

        $variant = $this->variantRepository->load(VariantId::fromString($command->variantId));

        $game = Game::create(
            GameId::fromId($command->gameId),
            GameName::fromString($command->name),
            $adjudicationTiming,
            $gameStartTiming,
            $variant,
            PlayerId::fromString($command->creatorId),
            $this->randomNumberGenerator->generate(...)
        );

        $this->gameRepository->save($game);
    }
}
