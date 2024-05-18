<?php

namespace Dnw\Game\Core\Infrastructure\Model\Game;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhaseModel extends Model
{
    public $table = 'game_phases';

    protected $casts = [
        'adjudication_time' => 'datetime',
    ];

    /**
     * @return HasMany<PhasePowerDataModel>
     */
    public function powerData(): HasMany
    {
        return $this->hasMany(PhasePowerDataModel::class, 'phase_id');
    }
}
