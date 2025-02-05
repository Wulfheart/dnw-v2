<?php

namespace Dnw\Game\Infrastructure\Model\Variant;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 */
class VariantModel extends Model
{
    use HasUlids;

    protected $table = 'game_variants';

    /**
     * @return HasMany<VariantPowerModel, $this>
     */
    public function powers(): HasMany
    {
        return $this->hasMany(VariantPowerModel::class, 'variant_id');
    }
}
