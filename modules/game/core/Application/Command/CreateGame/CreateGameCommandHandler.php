<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

use Dnw\Game\Core\Domain\Adapter\TimeProviderInterface;
use Dnw\Game\Core\Domain\Aggregate\Game;
use Dnw\Game\Core\Domain\Entity\MessageMode;
use Dnw\Game\Core\Domain\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Repository\MessageModeRepositoryInterface;
use Dnw\Game\Core\Domain\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming\AdjudicationTiming;
use Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming\NoAdjudicationWeekdayCollection;
use Dnw\Game\Core\Domain\ValueObject\AdjudicationTiming\PhaseLength;
use Dnw\Game\Core\Domain\ValueObject\Game\GameName;
use Dnw\Game\Core\Domain\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Core\Domain\ValueObject\MessageMode\MessageModeId;
use Dnw\Game\Core\Domain\ValueObject\PlayerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantId;
use PhpOption\None;

readonly class CreateGameCommandHandler
{
    public function __construct(
        private MessageModeRepositoryInterface $messageModeRepository,
        private VariantRepositoryInterface $variantRepository,
        private TimeProviderInterface $timeProvider,
        private GameRepositoryInterface $gameRepository,
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

        $game = Game::createWithRandomAssignments(
            GameName::fromString($command->name),
            $messageMode,
            $adjudicationTiming,
            $gameStartTiming,
            $variant,
            PlayerId::fromString($command->creatorId),
        );

        $this->gameRepository->save($game);
    }
}
