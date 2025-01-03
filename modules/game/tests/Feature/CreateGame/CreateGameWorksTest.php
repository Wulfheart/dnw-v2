<?php

namespace Dnw\Game\Test\Feature\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Application\Query\GetGameIdByName\GetGameIdByNameQuery;
use Dnw\Game\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\FakeEventDispatcher;
use Tests\TestCase;
use Wulfheart\Option\Option;
use Wulfheart\Option\ResultAsserter;

#[CoversNothing]
#[AllowLaravelTestCase]
class CreateGameWorksTest extends TestCase
{
    use FakeEventDispatcher;

    public function test(): void
    {
        $bus = $this->bootstrap(BusInterface::class);

        $variant = VariantFactory::standard();
        $colonial = VariantFactory::colonial();

        $variantRepo = $this->bootstrap(VariantRepositoryInterface::class);
        $variantRepo->save($variant);
        $variantRepo->save($colonial);

        $allVariantsResult = $bus->handle(new GetAllVariantsQuery());

        $variantId = $allVariantsResult->variants[0]->id;

        $result = $bus->handle(new CreateGameCommand(
            Id::generate(),
            'My Game',
            60,
            7,
            true,
            $variantId,
            true,
            Option::none(),
            false,
            true,
            [],
            Id::fromString($this->randomUser()->id)
        ));

        ResultAsserter::assertOk($result);

        $result = $bus->handle(new GetGameIdByNameQuery('My Game'));

        ResultAsserter::assertOk($result);

        $this->assertEventDispatched(GameCreatedEvent::class);
    }
}
