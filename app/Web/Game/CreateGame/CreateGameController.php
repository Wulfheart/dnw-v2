<?php

namespace App\Web\Game\CreateGame;

use App\Foundation\Auth\AuthInterface;
use App\Foundation\Id\IdGeneratorInterface;
use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Application\Command\CreateGame\CreateGameCommandResult;
use Dnw\Game\Application\Query\CanParticipateInAnotherGame\CanParticipateInAnotherGameQuery;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Wulfheart\Option\Option;

readonly class CreateGameController
{
    public function __construct(
        private BusInterface $bus,
        private AuthInterface $auth,
        private IdGeneratorInterface $idGenerator,
    ) {}

    public function show(Request $request): Response
    {
        $canParticipateInAnotherGame = $this->bus->handle(new CanParticipateInAnotherGameQuery($this->auth->getUserId()));

        $allVariants = $this->bus->handle(new GetAllVariantsQuery());
        $vm = CreateGameFormViewModel::fromLaravel($allVariants->variants, $canParticipateInAnotherGame->unwrap());

        return response()->view('game.create', ['vm' => $vm]);
    }

    public function store(StoreGameRequest $request): RedirectResponse
    {
        $creatorId = $this->auth->getUserId();

        $gameId = $this->idGenerator->generate();

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
            $creatorId,
        );
        $result = $this->bus->handle($command);
        if ($result->hasErr()) {
            match ($result->unwrapErr()) {
                CreateGameCommandResult::E_UNABLE_TO_LOAD_VARIANT => abort(Response::HTTP_NOT_FOUND),
                CreateGameCommandResult::E_NOT_ALLOWED_TO_CREATE_GAME => abort(Response::HTTP_FORBIDDEN),
            };
        }

        return response()->redirectTo(route('game.show', [$gameId]));
    }
}
