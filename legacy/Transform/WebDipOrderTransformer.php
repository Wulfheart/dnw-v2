<?php

namespace Dnw\Legacy\Transform;

use Dnw\Legacy\Transform\ResultData\GameData;
use Dnw\Legacy\Transform\ResultData\Power;
use Dnw\Legacy\Transform\ResultData\Turn;
use Exception;

final class WebDipOrderTransformer
{
    private function __construct(
        /** @var array<int, string> $territoryLookup */
        private array $territoryLookup,
        /** @var array<int, string> $powerLookup */
        private array $powerLookup,
    ) {}

    public static function build(): self
    {
        $powerLookup = [
            1 => 'Austria',
            2 => 'England',
            3 => 'France',
            4 => 'Germany',
            5 => 'Italy',
            6 => 'Russia',
            7 => 'Turkey',
        ];

        /** @var resource $file */
        $file = fopen(__DIR__ . '/data/wD_Territories.csv', 'r');
        // $header = fgetcsv($file);
        $lookup = [];
        while ($row = fgetcsv($file)) {
            $lookup[(int) $row[0]] = $row[1] ?? throw new Exception();
        }

        return new self($lookup, $powerLookup);
    }

    public function transformGameById(int $gameId): GameData
    {
        /** @var resource $file */
        $file = fopen(__DIR__ . '/data/wD_MovesArchive.csv', 'r');
        $header = fgetcsv($file);
        $lines = [];
        while ($row = fgetcsv($file)) {
            // @phpstan-ignore-next-line
            $data = array_combine($header, $row);
            if ((int) $data['gameID'] === $gameId) {
                $lines[] = MovesArchiveLine::fromArray($data);
            }
        }

        return $this->transformGame($gameId, $lines);
    }

    /**
     * @param  array<MovesArchiveLine>  $lines
     */
    private function transformGame(int $gameId, array $lines): GameData
    {
        $collected = collect($lines)->groupBy(
            fn (MovesArchiveLine $line) => $line->turn
        )->map(
            fn (\Illuminate\Support\Collection $c) => $c->groupBy(
                fn (MovesArchiveLine $line) => $line->countryId
            )
        )->toArray();

        $turns = [];
        foreach ($collected as $turnNumber => $powers) {
            $transformedPowers = [];
            foreach ($powers as $power => $moves) {
                $powerName = $this->powerLookup[$power] ?? throw new Exception("Unknown power $power");
                $transformedMoves = [];
                foreach ($moves as $move) {
                    $transformedMoves[] = $this->transformToString($move);
                }
                $transformedPowers[] = new Power($powerName, $transformedMoves);
            }

            $turns[] = new Turn($turnNumber, $transformedPowers);
        }

        return new GameData((string) $gameId, $turns);
    }

    private function transformToString(MovesArchiveLine $line): string
    {
        return '';
    }
}
