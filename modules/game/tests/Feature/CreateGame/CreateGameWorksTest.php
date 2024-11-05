<?php

namespace Dnw\Game\Test\Feature\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Application\Listener\GameCreatedListener;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsResult;
use Dnw\Game\Application\Query\GetGameIdByName\GetGameIdByNameQuery;
use Dnw\Game\Application\Query\GetGameIdByName\GetGameIdByNameQueryResult;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\TestCase;
use Wulfheart\Option\ResultAsserter;

#[CoversNothing]
#[AllowLaravelTestCase]
class CreateGameWorksTest extends TestCase
{
    public function test(): void
    {
        $bus = $this->bootstrap(BusInterface::class);

        $variant = VariantFactory::standard();
        $colonial = VariantFactory::colonial();

        $variantRepo = $this->bootstrap(VariantRepositoryInterface::class);
        $variantRepo->save($variant);
        $variantRepo->save($colonial);

        /** @var GetAllVariantsResult $allVariantsResult */
        $allVariantsResult = $bus->handle(new GetAllVariantsQuery());

        $variantId = $allVariantsResult->variants[0]->id;

        $this->actingAs($this->randomUser())->post(route('game.store'), [
            'name' => 'My Game',
            'variantId' => $variantId,
            'phaseLengthInMinutes' => 60,
            'joinLengthInDays' => 7,
            'startWhenReady' => true,
        ]);

        /** @var GetGameIdByNameQueryResult $result */
        $result = $bus->handle(new GetGameIdByNameQuery('My Game'));

        ResultAsserter::assertOk($result);
        $this->assertListenerIsQueued(GameCreatedListener::class);
    }
}
