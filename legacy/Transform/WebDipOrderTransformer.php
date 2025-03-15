<?php

namespace Dnw\Legacy\Transform;

use Dnw\Legacy\Transform\ResultData\GameData;
use Dnw\Legacy\Transform\ResultData\Power;
use Dnw\Legacy\Transform\ResultData\Turn;
use Exception;
use Illuminate\Support\Collection;

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
            1 => 'England',
            2 => 'France',
            3 => 'Italy',
            4 => 'Germany',
            5 => 'Austria',
            6 => 'Turkey',
            7 => 'Russia',
        ];

        /** @var resource $file */
        $file = fopen(__DIR__ . '/data/wD_Territories.csv', 'r');
        $header = fgetcsv($file);
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
            fn (MovesArchiveLine $line) => str_pad((string) $line->turn, 3, '0', STR_PAD_LEFT) . '_' . (str_starts_with($line->type, 'Build') ? '1_build' : '0_orders')
        )->map(
            fn (Collection $c) => $c->groupBy(
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

        // 'Hold','Move','Support hold','Support move','Convoy','Retreat','Disband','Build Army','Build Fleet','Wait','Destroy'
        return match ($line->type) {
            'Hold' => "{$line->unitType} {$this->lookupTerritory($line->terrId)} H",
            'Move' => "{$line->unitType} {$this->lookupTerritory($line->terrId)} - {$this->lookupTerritory($line->getFromTerrId())}",
            'Support hold' => "{$line->unitType} {$this->lookupTerritory($line->getFromTerrId())} S {$this->lookupTerritory($line->terrId)}",
            'Support move' => "{$line->unitType} {$this->lookupTerritory($line->terrId)} S {$this->lookupTerritory($line->getFromTerrId())} - {$this->lookupTerritory($line->getToTerrId())}",
            'Convoy' => "{$line->unitType} {$this->lookupTerritory($line->terrId)} C {$this->lookupTerritory($line->getToTerrId())} - {$this->lookupTerritory($line->getFromTerrId())}",
            'Retreat' => "{$line->unitType} {$this->lookupTerritory($line->getFromTerrId())} R {$this->lookupTerritory($line->terrId)}",
            'Disband' => "{$line->unitType} {$this->lookupTerritory($line->terrId)} D",
            'Build Army' => "Build A {$this->lookupTerritory($line->terrId)}",
            'Build Fleet' => "Build F {$this->lookupTerritory($line->terrId)}",
            'Destroy' => "Destroy {$this->lookupTerritory($line->terrId)}",
            'Wait' => 'Wait',
            default => throw new Exception("Unknown type $line->type"),
        };
    }

    private function lookupTerritory(int $territoryId): string
    {
        return $this->territoryLookup[$territoryId] ?? throw new Exception("Unknown territory $territoryId");
    }
}
