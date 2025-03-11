<?php

namespace Dnw\Game\Application\Command\CreateGame;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Domain\Adapter\RandomNumberGenerator\FakeRandomNumberGenerator;
use Dnw\Game\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Domain\Game\Repository\Game\Impl\InMemory\InMemoryGameRepository;
use Dnw\Game\Domain\Game\Test\Asserter\GameAsserter;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Player\Player;
use Dnw\Game\Domain\Player\Repository\Player\Impl\InMemoryPlayerRepository;
use Dnw\Game\Domain\Player\Repository\Player\Impl\SimpleInMemoryPlayerRepository;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Repository\Impl\InMemory\InMemoryVariantRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Wulfheart\Option\Option;
use Wulfheart\Option\ResultAsserter;

#[CoversClass(CreateGameCommand::class)]
#[CoversClass(CreateGameCommandHandler::class)]
class CreateGameCommandHandlerTest extends TestCase
{
    public function test_cannot_create_a_game_when_user_has_already_three_games_they_are_playing_in(): void
    {
        $playerId = PlayerId::new();
        $playerRepository = new SimpleInMemoryPlayerRepository([new Player($playerId, 3)]);

        $handler = new CreateGameCommandHandler(
            new InMemoryVariantRepository(),
            FakeTimeProvider::now(),
            new InMemoryGameRepository(new FakeEventDispatcher()),
            $playerRepository,
            new FakeRandomNumberGenerator(3),
            new NullLogger()
        );

        $result = $handler->handle(new CreateGameCommand(
            Id::generate(),
            '::NAME::',
            60,
            6,
            true,
            Id::generate(),
            true,
            Option::none(),
            false,
            false,
            [],
            $playerId->toId(),
        ));

        ResultAsserter::assertErrIs($result, CreateGameCommandResult::E_NOT_ALLOWED_TO_CREATE_GAME);
    }

    public function test_cannot_create_a_game_with_an_unloadable_variant(): void
    {
        $variantRepository = new InMemoryVariantRepository();

        $playerId = PlayerId::new();
        $gameId = GameId::new();

        $command = new CreateGameCommand(
            $gameId->toId(),
            'Das ist ein Spiel',
            60,
            5,
            true,
            '::KEY::',
            true,
            Option::none(),
            true,
            true,
            [0],
            $playerId->toId()
        );

        $timeProvider = FakeTimeProvider::now();
        $gameRepository = new InMemoryGameRepository(
            new FakeEventDispatcher(),
            []
        );
        $playerRepository = new InMemoryPlayerRepository(
            $gameRepository
        );
        $randomNumberGenerator = new FakeRandomNumberGenerator(0);

        $logger = new NullLogger();

        $handler = new CreateGameCommandHandler(
            $variantRepository,
            $timeProvider,
            $gameRepository,
            $playerRepository,
            $randomNumberGenerator,
            $logger
        );

        $result = $handler->handle($command);

        ResultAsserter::assertErrIs($result, CreateGameCommandResult::E_UNABLE_TO_LOAD_VARIANT);
    }

    public function test_handle(): void
    {
        $variant = VariantFactory::standard();
        $variantRepository = new InMemoryVariantRepository(
            [$variant]
        );

        $playerId = PlayerId::new();
        $gameId = GameId::new();

        $command = new CreateGameCommand(
            $gameId->toId(),
            'Das ist ein Spiel',
            60,
            5,
            true,
            $variant->key,
            true,
            Option::none(),
            true,
            true,
            [0],
            $playerId->toId()
        );

        $timeProvider = new FakeTimeProvider('2021-01-01 00:00:00');
        $gameRepository = new InMemoryGameRepository(
            new FakeEventDispatcher(),
            []
        );
        $playerRepository = new InMemoryPlayerRepository(
            $gameRepository
        );
        $randomNumberGenerator = new FakeRandomNumberGenerator(0);

        $handler = new CreateGameCommandHandler(
            $variantRepository,
            $timeProvider,
            $gameRepository,
            $playerRepository,
            $randomNumberGenerator,
            new NullLogger(),
        );

        $handler->handle($command);

        $game = $gameRepository->load($gameId)->unwrap();

        GameAsserter::assertThat($game)
            ->hasGameId($gameId)
            ->powerIdHasPlayerId($game->powerCollection->getOffset(0)->powerId, $playerId);

    }
}
