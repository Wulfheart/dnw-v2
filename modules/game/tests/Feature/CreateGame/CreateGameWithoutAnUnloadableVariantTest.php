<?php

namespace Dnw\Game\Test\Feature\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Application\Listener\GameCreatedListener;
use Dnw\Game\Application\Query\GetGameIdByName\GetGameIdByNameQuery;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\FakeQueue;
use Tests\LaravelTestCase;
use Wulfheart\Option\ResultAsserter;

#[CoversNothing]
#[AllowLaravelTestCase]
class CreateGameWithoutAnUnloadableVariantTest extends LaravelTestCase
{
    use FakeQueue;

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

        $result = $bus->handle(new GetGameIdByNameQuery('My Game'));

        ResultAsserter::assertErr($result);
        $this->assertListenerIsNotQueued(GameCreatedListener::class);
    }
}
