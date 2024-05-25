<?php

namespace Dnw\Game\Http\Controllers\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\Core\Application\Query\GetAllVariants\VariantDto;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Tests\Factory\VariantFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Std\Option;

class CreateGameController
{
    public function __construct(
        private BusInterface $bus,
        private VariantRepositoryInterface $variantRepository,
    ) {

    }

    public function get(): Response
    {
        /** @var array<VariantDto> $variants */
        $variants = $this->bus->handle(new GetAllVariantsQuery());

        $viewModel = CreateGameFormViewModel::fromLaravel($variants);

        return new Response(view('game::create', $viewModel));
    }

    public function post(CreateGameRequest $createGameRequest): RedirectResponse
    {
        $gameId = Id::generate();

        $command = new CreateGameCommand(
            $gameId,
            $createGameRequest->input(CreateGameRequest::KEY_NAME),
            $createGameRequest->input(CreateGameRequest::KEY_PHASE_LENGTH_IN_MINUTES),
            $createGameRequest->input(CreateGameRequest::KEY_JOIN_LENGTH_IN_DAYS),
            $createGameRequest->input(CreateGameRequest::KEY_START_WHEN_READY),
            Id::fromString($createGameRequest->input(CreateGameRequest::KEY_VARIANT_ID)),
            $createGameRequest->input(CreateGameRequest::KEY_RANDOM_POWER_ASSIGNMENTS),
            Option::fromNullable($createGameRequest->input(CreateGameRequest::KEY_SELECTED_POWER_ID))->mapIntoOption(fn (string $id) => Id::fromString($id)),
            $createGameRequest->input(CreateGameRequest::KEY_IS_RANKED),
            $createGameRequest->input(CreateGameRequest::KEY_IS_ANONYMOUS),
            $createGameRequest->input(CreateGameRequest::KEY_WEEKDAYS_WITHOUT_ADJUDICATION, []),
            Id::generate(),
        );

        $variant = VariantFactory::standard();
        $variant->id = VariantId::fromString($createGameRequest->input(CreateGameRequest::KEY_VARIANT_ID));

        $this->variantRepository->save($variant);

        $this->bus->handle($command);

        return new RedirectResponse('/');
    }
}
