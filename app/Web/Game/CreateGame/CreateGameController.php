<?php

namespace App\Web\Game\CreateGame;

use App\Models\User;
use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Application\Command\CreateGame\CreateGameResult;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Wulfheart\Option\Option;

readonly class CreateGameController
{
    public function __construct(
        private BusInterface $bus,
    ) {}

    public function show(Request $request): Response
    {
        // TODO: Add a query to determine if a user can create a game in order to show a hint later

        /** @var GetAllVariantsResult $allVariants */
        $allVariants = $this->bus->handle(new GetAllVariantsQuery());
        $vm = CreateGameFormViewModel::fromLaravel($allVariants->variants);

        return response()->view('game.create', ['vm' => $vm]);
    }

    public function store(StoreGameRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $gameId = Id::generate();

        $command = new CreateGameCommand(
            $gameId,
            $request->string(StoreGameRequest::KEY_NAME),
            $request->integer(StoreGameRequest::PHASE_LENGTH_IN_MINUTES),
            $request->integer(StoreGameRequest::KEY_JOIN_LENGTH_IN_DAYS),
            $request->boolean(StoreGameRequest::KEY_START_WHEN_READY),
            Id::fromString($request->string(StoreGameRequest::KEY_VARIANT_ID)),
            true,
            Option::none(),
            true,
            false,
            [],
            Id::fromString($user->id),
        );
        /** @var CreateGameResult $result */
        $result = $this->bus->handle($command);
        if ($result->hasErr()) {
            match ($result->unwrapErr()) {
                CreateGameResult::E_UNABLE_TO_LOAD_VARIANT => abort(Response::HTTP_NOT_FOUND),
                CreateGameResult::E_NOT_ALLOWED_TO_CREATE_GAME => abort(Response::HTTP_FORBIDDEN),
            };
        }

        return response()->redirectTo(route('game.show', [$gameId]));
    }
}
