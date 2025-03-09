<?php

namespace Dnw\Game\Domain\Variant\Repository\Impl\LaravelVariantRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantModel extends Model
{
    protected $table = 'game_variants';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return HasMany<VariantPowerModel, $this>
     */
    public function powers(): HasMany
    {
        return $this->hasMany(VariantPowerModel::class, 'variant_key');
    }
}
