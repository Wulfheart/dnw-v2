<?php

namespace Dnw\Game\Http\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Bus\FakeBus;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Core\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Core\Application\Command\CreateGame\CreateGameResult;
use PHPUnit\Framework\Attributes\CoversClass;
use Wulfeart\Option\Option;
use Tests\TestCase;

#[CoversClass(CreateGameController::class)]
#[AllowLaravelTestCase]
class CreateGameControllerTest extends TestCase
{
    public function test_show(): void
    {
        $response = $this->actingAs($this->randomUser())->get(route('game.create'));
        $response->assertStatus(200);
    }

    public function test_store(): void
    {
        $gameId = Id::generate();
        $variantId = Id::generate();
        $userId = Id::generate();

        $this->assertActionUsesFormRequest(CreateGameController::class, 'store', StoreGameRequest::class);

        $command = new CreateGameCommand(
            $gameId,
            '::GAME_NAME::',
            10,
            7,
            true,
            $variantId,
            true,
            Option::none(),
            true,
            false,
            [],
            $userId
        );
        $fakeBus = new FakeBus(
            [
                function (CreateGameCommand $c) use ($command): bool {
                    $expected = (array) $command;
                    $actual = (array) $c;

                    unset($expected['gameId'], $actual['gameId']);

                    return $expected == $actual;
                }, CreateGameResult::ok()],
        );
        $this->instance(BusInterface::class, $fakeBus);

        $this->actingAs($this->userWithId($userId))->post(
            action([CreateGameController::class, 'store']),
            StoreGameRequestFactory::new()
                ->name('::GAME_NAME::')
                ->phaseLengthInMinutes(10)
                ->joinLengthInDays(7)
                ->startWhenReady(true)
                ->variantId((string) $variantId)
                ->create()
        )->assertRedirect('/');
    }

    public function test_store_throws_404_if_variant_cannot_be_loaded(): void
    {
        $fakeBus = new FakeBus(
            [
                CreateGameCommand::class,
                CreateGameResult::err(CreateGameResult::E_UNABLE_TO_LOAD_VARIANT),
            ],
        );
        $this->instance(BusInterface::class, $fakeBus);

        $this->actingAs($this->randomUser())->post(
            action([CreateGameController::class, 'store']),
            StoreGameRequestFactory::new()->create()
        )->assertNotFound();
    }

    public function test_store_throws_403_if_user_cannot_create_game(): void
    {
        $fakeBus = new FakeBus(
            [CreateGameCommand::class, CreateGameResult::err(CreateGameResult::E_NOT_ALLOWED_TO_CREATE_GAME)],
        );
        $this->instance(BusInterface::class, $fakeBus);

        $this->actingAs($this->randomUser())->post(
            action([CreateGameController::class, 'store']),
            StoreGameRequestFactory::new()->create()
        )->assertForbidden();
    }
}
