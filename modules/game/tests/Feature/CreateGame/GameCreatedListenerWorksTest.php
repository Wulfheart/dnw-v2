<?php

namespace Dnw\Game\Test\Feature\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Application\Listener\GameCreatedListener;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsResult;
use Dnw\Game\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Test\Feature\Fake\FakeWebDipAdjudicatorImplementation;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\FakeEventDispatcher;
use Tests\TestCase;
use Wulfheart\Option\Option;
use Wulfheart\Option\ResultAsserter;

#[CoversNothing]
#[AllowLaravelTestCase]
final class GameCreatedListenerWorksTest extends TestCase
{
    use FakeEventDispatcher;
    use FakeWebDipAdjudicatorImplementation;

    public function test_works(): void
    {
        $this->fakeWebDipAdjudicatorImplementation(__DIR__ . 'Fixture');
        $bus = $this->bootstrap(BusInterface::class);

        $gameId = Id::generate();

        $creatorId = Id::fromString($this->randomUser()->id);

        $this->startGame($bus, $gameId, $creatorId);

        $listener = $this->bootstrap(GameCreatedListener::class);

        $listener->handle(new GameCreatedEvent($gameId, $creatorId));

    }

    public function startGame(BusInterface $bus, Id $gameId, Id $creatorId): void
    {
        $variant = VariantFactory::standard();
        $colonial = VariantFactory::colonial();

        $variantRepo = $this->bootstrap(VariantRepositoryInterface::class);
        $variantRepo->save($variant);
        $variantRepo->save($colonial);

        /** @var GetAllVariantsResult $allVariantsResult */
        $allVariantsResult = $bus->handle(new GetAllVariantsQuery());

        $variantId = $variant->id;

        $result = $bus->handle(new CreateGameCommand(
            $gameId,
            'My Game',
            60,
            7,
            true,
            $variantId->toId(),
            true,
            Option::none(),
            false,
            true,
            [],
            $creatorId
        ));

        ResultAsserter::assertOk($result);
    }
}
