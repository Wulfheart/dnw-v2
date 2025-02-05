<?php

namespace Dnw\Game\Infrastructure\Model\Variant;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 */
class VariantPowerModel extends Model
{
    use HasUlids;

    protected $table = 'game_variant_powers';
}
