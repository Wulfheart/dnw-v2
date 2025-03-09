<?php

namespace Dnw\Game\Domain\Variant\Repository\Impl\LaravelVariantRepository;

use Illuminate\Database\Eloquent\Model;

class VariantPowerModel extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'game_variant_powers';
}
