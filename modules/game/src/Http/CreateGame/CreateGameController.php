<?php

namespace Dnw\Game\Http\CreateGame;

use App\Models\User;
use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Core\Application\Command\CreateGame\CreateGameResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class CreateGameController
{
    public function __construct(
        private BusInterface $bus,
    ) {}

    public function show(Request $request): void {
        // TODO: Add a query to determine if a user can create a game
    }

    public function store(StoreGameRequest $request): RedirectResponse
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
        /** @var CreateGameResult $result */
        $result = $this->bus->handle($command);
        if ($result->hasErr()) {
            match ($result->unwrapErr()) {
                CreateGameResult::E_UNABLE_TO_LOAD_VARIANT => abort(404),
                CreateGameResult::E_NOT_ALLOWED_TO_CREATE_GAME => abort(Response::HTTP_FORBIDDEN),
            };
        }

        return response()->redirectTo(route(''));
    }
}
