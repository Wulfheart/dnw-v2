<?php

namespace Dnw\Game\Tests\Feature\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Core\Application\Listener\GameCreatedListener;
use Dnw\Game\Core\Application\Query\GetGameIdByName\GetGameIdByNameQuery;
use Dnw\Game\Core\Application\Query\GetGameIdByName\GetGameIdByNameQueryResult;
use PHPUnit\Framework\Attributes\CoversNothing;
use Wulfeart\Option\ResultAsserter;
use Tests\TestCase;

#[CoversNothing]
#[AllowLaravelTestCase]
class CreateGameWithoutAnUnloadableVariantTest extends TestCase
{
    public function test(): void
    {
        $bus = $this->bootstrap(BusInterface::class);

        $response = $this->actingAs($this->randomUser())->post(route('game.store'), [
            'name' => 'My Game',
            'variantId' => Id::generate(),
            'phaseLengthInMinutes' => 60,
            'joinLengthInDays' => 7,
            'startWhenReady' => true,
        ]);

        $response->assertNotFound();

        /** @var GetGameIdByNameQueryResult $result */
        $result = $bus->handle(new GetGameIdByNameQuery('My Game'));

        ResultAsserter::assertErr($result);
        $this->assertListenerIsNotQueued(GameCreatedListener::class);
    }
}
