<?php

namespace Dnw\Game\Infrastructure\Model\Game;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PhasePowerDataModel extends Model
{
    use HasUlids;

    public $table = 'game_phase_power_data';

    public $casts = [
        'order_collection' => 'array',
        'applied_orders' => 'array',
        'orders_needed' => 'boolean',
        'marked_as_ready' => 'boolean',
        'is_winner' => 'boolean',
    ];
}
