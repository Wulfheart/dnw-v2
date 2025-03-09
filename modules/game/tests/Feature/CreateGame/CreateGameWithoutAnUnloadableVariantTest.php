<?php

namespace Dnw\Game\Test\Feature\CreateGame;

use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Listener\GameCreatedListener;
use Dnw\Game\Application\Query\GetGameIdByName\GetGameIdByNameQuery;
use PHPUnit\Framework\Attributes\CoversNothing;
use Tests\FakeQueue;
use Tests\HttpTestCase;
use Wulfheart\Option\ResultAsserter;

#[CoversNothing]

class CreateGameWithoutAnUnloadableVariantTest extends HttpTestCase
{
    use FakeQueue;

    public function test(): void
    {
        $response = $this->actingAs($this->randomUser())->post(route('game.store'), [
            'name' => 'My Game',
            'variantId' => Id::generate(),
            'phaseLengthInMinutes' => 60,
            'joinLengthInDays' => 7,
            'startWhenReady' => true,
        ]);

        $response->assertNotFound();

        $result = $this->bus->handle(new GetGameIdByNameQuery('My Game'));

        ResultAsserter::assertErr($result);
        $this->assertListenerIsNotQueued(GameCreatedListener::class);
    }
}
