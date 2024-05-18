<?php

namespace Dnw\Game\Core\Infrastructure\Model\Game;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PowerModel extends Model
{
    use HasUlids;

    public $table = 'game_powers';
}
