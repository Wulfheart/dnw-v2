<?php

namespace Dnw\Game\Core\Infrastructure\Model\Game;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GameModel extends Model
{
    use HasUlids;

    public $table = 'game_games';

    /**
     * @var array<int|string, string>
     */
    public $casts = [
        'game_start_timing_start_of_join_phase' => 'datetime',
        'adjudication_timing_no_adjudication_weekdays' => 'array',
        'variant_data_variant_power_ids' => 'collection',
        'random_power_assignments' => 'boolean',
        'game_start_timing_start_when_ready' => 'boolean',
    ];

    /**
     * @return HasMany<PhaseModel>
     */
    public function phases(): HasMany
    {
        return $this->hasMany(PhaseModel::class, 'game_id');
    }

    /**
     * @return HasOne<PhaseModel>
     */
    public function currentPhase(): HasOne
    {
        return $this->hasOne(PhaseModel::class, 'game_id')
            ->ofMany('ordinal_number', 'max');
    }

    /**
     * @return HasOne<PhaseModel>
     */
    public function lastPhase(): HasOne
    {
        return $this->hasOne(PhaseModel::class, 'game_id')
            ->orderByDesc('ordinal_number')
            ->offset(1)
            ->limit(1);
    }

    /**
     * @return HasMany<PowerModel>
     */
    public function powers(): HasMany
    {
        return $this->hasMany(PowerModel::class, 'game_id');
    }
}
