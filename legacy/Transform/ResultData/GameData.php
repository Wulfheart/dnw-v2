<?php

namespace Dnw\Legacy\Transform\ResultData;

use JsonSerializable;

final readonly class GameData implements JsonSerializable
{
    /**
     * @var array<Turn>
     */
    public array $turns;

    /**
     * @param  array<Turn>  $turns
     */
    public function __construct(
        public string $id,
        array $turns
    ) {
        usort($turns, fn (Turn $a, Turn $b) => $a->index <=> $b->index);
        $this->turns = $turns;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'turns' => $this->turns,
        ];
    }
}
