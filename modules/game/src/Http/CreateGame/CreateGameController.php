<?php

namespace Dnw\Game\Http\CreateGame;

use App\Models\User;
use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Application\Command\CreateGame\CreateGameCommand;
use Illuminate\Http\Request;
use Std\Option;

readonly class CreateGameController
{
    public function __construct(
        private BusInterface $bus,
    ) {}

    public function show(Request $request): void {
        // TODO: Show a message if a user cannot create a game due to some rules
    }

    public function store(StoreGameRequest $request): void
    {
        /** @var User $user */
        $user = $request->user();

        $command = new CreateGameCommand(
            Id::generate(),
            $request->string('name'),
            $request->integer('phaseLengthInMinutes'),
            $request->integer('joinLengthInDays'),
            $request->boolean('startWhenReady'),
            Id::fromString($request->string('variantId')),
            $request->boolean('randomPowerAssignments'),
            Id::fromNullable($request->string('selectedVariantPowerId')),
            $request->boolean('isRanked'),
            $request->boolean('isAnonymous'),
            $request->input('weekdaysWithoutAdjudication'),
            Id::fromString($user->id),
        );
        $this->bus->handle($command);
    }
}
