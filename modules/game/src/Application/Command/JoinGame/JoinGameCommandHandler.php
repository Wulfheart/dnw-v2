<?php

namespace Dnw\Game\Application\Command\JoinGame;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;
use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Psr\Log\LoggerInterface;

readonly class JoinGameCommandHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private TimeProviderInterface $timeProvider,
        private RandomNumberGeneratorInterface $randomNumberGenerator,
        private LoggerInterface $logger,
    ) {}

    public function handle(JoinGameCommand $command): JoinGameResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromString($command->gameId));
        if ($gameResult->hasErr()) {
            $this->logger->info('Game not found', ['gameId' => $command->gameId]);

            return JoinGameResult::err(JoinGameResult::E_GAME_NOT_FOUND);
        }
        $game = $gameResult->unwrap();

        $game->join(
            PlayerId::fromId($command->userId),
            $command->variantPowerId->mapIntoOption(fn (Id $id) => VariantPowerId::fromId($id)),
            $this->timeProvider->getCurrentTime(),
            $this->randomNumberGenerator->generate(...)
        );

        $this->gameRepository->save($game);

        return JoinGameResult::ok();
    }
}