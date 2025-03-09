<?php

namespace Dnw\Game\Infrastructure\Model\Variant;

use Illuminate\Database\Eloquent\Model;

class VariantPowerModel extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'game_variant_powers';
}
