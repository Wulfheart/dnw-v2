<?php

namespace Dnw\Legacy\Transform;

use Exception;

final readonly class MovesArchiveLine
{
    public int $gameId;

    public int $turn;

    public int $countryId;

    public int $terrId;

    public ?int $toTerrId;

    public ?int $fromTerrId;

    public function __construct(
        string $gameId,
        string $turn,
        string $countryId,
        public ?string $unitType,
        public string $type,
        string $terrId,
        ?string $fromTerrId,
        ?string $toTerrId,
    ) {
        $this->gameId = (int) $gameId;
        $this->turn = (int) $turn;
        $this->countryId = (int) $countryId;
        $this->terrId = (int) $terrId;
        $this->fromTerrId = $fromTerrId ? (int) $fromTerrId : null;
        $this->toTerrId = $toTerrId ? (int) $toTerrId : null;
    }

    /**
     * @param  array<string, ?string>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['gameID'] ?? throw new Exception(),
            $data['turn'] ?? throw new Exception(),
            $data['countryID'] ?? throw new Exception(),
            $data['unitType'] ?? throw new Exception(),
            $data['type'] ?? throw new Exception(),
            $data['terrID'],
            $data['toTerrID'],
            $data['fromTerrID'],
        );
    }
}
