<?php

namespace Dnw\Game\Domain\Game\Repository\Phase\Impl\Laravel;

use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 */
class PhaseModel extends Model
{
    use HasUlids;

    public $table = 'game_phases';

    protected $casts = [
        'type' => PhaseTypeEnum::class,
    ];

    /**
     * @return HasMany<PhasePowerDataModel, $this>
     */
    public function powerData(): HasMany
    {
        return $this->hasMany(PhasePowerDataModel::class, 'phase_id');
    }
}
