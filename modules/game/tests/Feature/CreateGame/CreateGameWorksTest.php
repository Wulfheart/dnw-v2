<?php

namespace Dnw\Game\Tests\Feature\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Core\Application\Listener\GameCreatedListener;
use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsResult;
use Dnw\Game\Core\Application\Query\GetGameIdByName\GetGameIdByNameQuery;
use Dnw\Game\Core\Application\Query\GetGameIdByName\GetGameIdByNameQueryResult;
use Dnw\Game\Database\Seeders\VariantSeeder;
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
        $this->seed(VariantSeeder::class);

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
