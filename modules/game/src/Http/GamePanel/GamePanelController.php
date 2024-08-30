<?php

namespace Dnw\Game\Http\GamePanel;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Core\Application\Query\GetGameById\Dto\GameStateEnum;
use Dnw\Game\Core\Application\Query\GetGameById\GetGameByIdQuery;
use Dnw\Game\Core\Application\Query\GetGameById\GetGameByIdQueryResultData;
use Dnw\Game\Core\Application\Query\GetGameIdByName\GetGameIdByNameQueryResult;
use Dnw\Game\Helper\PhaseLengthFormatter;
use Dnw\Game\Http\ViewModel\GameInformationViewModel;
use Illuminate\Http\Request;

class GamePanelController
{
    public function __construct(
        private BusInterface $bus,
        private PhaseLengthFormatter $phaseLengthFormatter
    ) {}

    public function show(Request $request, string $id)
    {
        /** @var GetGameIdByNameQueryResult $result */
        $result = $this->bus->handle(new GetGameByIdQuery(Id::fromString($id)));
        if ($result->hasErr()) {
            return abort(404);
        }
        /** @var GetGameByIdQueryResultData $data */
        $data = $result->unwrap();

        if ($data->state === GameStateEnum::CREATED) {
            $gameInfoViewModel = new GameInformationViewModel(
                $data->name,
                null,
                'Pre-game',
                $data->variantName,
                'foo',
                '',
                $this->phaseLengthFormatter->formatMinutes($data->phaseLengthInMinutes),
                '/phase',
                'Start',
                (string) $data->nextPhaseStart->toUnixTime(),
                $this->phaseLengthFormatter->formatDateTime($data->nextPhaseStart)
            );
            $viewModel = new GamePanelCreatedViewModel(
                $gameInfoViewModel,
                'Refresh'
            );

        }

        return null;
    }
}
