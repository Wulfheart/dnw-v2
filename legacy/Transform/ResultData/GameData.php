<?php

namespace Dnw\Legacy\Transform\ResultData;

final readonly class GameData
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
}
