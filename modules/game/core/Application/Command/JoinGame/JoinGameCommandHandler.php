<?php

namespace Dnw\Game\Core\Application\Command\JoinGame;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;

readonly class JoinGameCommandHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private TimeProviderInterface $timeProvider,
        private RandomNumberGeneratorInterface $randomNumberGenerator,
    ) {}

    public function handle(JoinGameCommand $command): JoinGameResult
    {
        $gameResult = $this->gameRepository->load(GameId::fromString($command->gameId));
        if ($gameResult->hasErr()) {
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
