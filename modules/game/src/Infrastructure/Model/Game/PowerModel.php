<?php

namespace Dnw\Game\Infrastructure\Model\Game;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowerModel extends Model
{
    use HasUlids;

    public $table = 'game_powers';

    /**
     * @return BelongsTo<GameModel, self>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(GameModel::class, 'game_id');
    }
}
