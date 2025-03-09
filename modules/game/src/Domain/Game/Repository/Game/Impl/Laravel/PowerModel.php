<?php

namespace Dnw\Game\Domain\Game\Repository\Game\Impl\Laravel;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 */
class PowerModel extends Model
{
    use HasUlids;

    public $table = 'game_powers';

    /**
     * @return BelongsTo<GameModel, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(GameModel::class, 'game_id');
    }
}
