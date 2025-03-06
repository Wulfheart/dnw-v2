<?php

namespace Dnw\Game\Application\Query\GetGameById;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Domain\Game\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Game\Test\Factory\GameStartTimingFactory;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Infrastructure\Repository\Variant\InMemoryVariantRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Log\NullLogger;
use Tests\LaravelTestCase;
use Wulfheart\Option\ResultAsserter;

#[CoversClass(GetGameByIdQueryHandler::class)]
#[AllowLaravelTestCase]
class GetGameByIdQueryHandlerTest extends LaravelTestCase
{
    public function test_handle_game_created(): void
    {
        $variant = VariantFactory::standard();

        $dateTime = new DateTime('2024-12-12 19:03');

        $game = GameBuilder::initialize(
            gameStartTiming: GameStartTimingFactory::build(startOfJoinPhase: $dateTime),
            variant: $variant
        )->build();

        $gameRepo = new InMemoryGameRepository(new FakeEventDispatcher(), [$game]);
        $variantRepo = new InMemoryVariantRepository([$variant]);

        $timeProvider = new FakeTimeProvider($dateTime);

        $handler = new GetGameByIdQueryHandler($gameRepo, $variantRepo, $timeProvider, new NullLogger());

        $result = $handler->handle(new GetGameByIdQuery($game->gameId->toId(), Id::generate()));
        ResultAsserter::assertOk($result);

    }
}
