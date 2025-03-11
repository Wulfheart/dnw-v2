<?php

namespace Dnw\Game\Application\Query\GetGameIdByName;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Application\Query\GetGameById\GetGameByIdQueryResult;
use Dnw\Game\Domain\Game\Repository\Game\Impl\InMemory\InMemoryGameRepository;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Game\GameName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Wulfheart\Option\ResultAsserter;

#[CoversClass(GetGameIdByNameQueryHandler::class)]
class GetGameIdByNameQueryHandlerTest extends TestCase
{
    public function test_returns_error_if_game_is_not_found(): void
    {
        $repository = new InMemoryGameRepository(new FakeEventDispatcher());

        $handler = new GetGameIdByNameQueryHandler($repository);

        $result = $handler->handle(new GetGameIdByNameQuery('::NAME::'));

        ResultAsserter::assertErrIs($result, GetGameByIdQueryResult::E_GAME_NOT_FOUND);
    }

    public function test_returns_game_id(): void
    {
        $gameId = GameId::new();
        $game = GameBuilder::initialize(
            gameId: $gameId,
            gameName: GameName::fromString('::NAME::')
        )->build();
        $gameRepository = new InMemoryGameRepository(
            new FakeEventDispatcher(),
            [$game]
        );

        $handler = new GetGameIdByNameQueryHandler($gameRepository);

        $result = $handler->handle(new GetGameIdByNameQuery('::NAME::'));

        ResultAsserter::assertOk($result);

        $this->assertEquals($gameId->toId(), $result->unwrap());

    }
}
