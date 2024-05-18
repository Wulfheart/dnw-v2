<?php

namespace Dnw\Game\Core\Infrastructure\Model\Game;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhaseModel extends Model
{
    use HasUlids;

    public $table = 'game_phases';

    protected $casts = [];

    /**
     * @return HasMany<PhasePowerDataModel>
     */
    public function powerData(): HasMany
    {
        return $this->hasMany(PhasePowerDataModel::class, 'phase_id');
    }
}
