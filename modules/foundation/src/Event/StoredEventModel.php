<?php

namespace Dnw\Foundation\Event;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class StoredEventModel extends Model
{
    use HasUlids;
    protected $casts = [
      'recorded_at' => 'datetime',
    ];

    protected $table = 'foundation_event_store';
    public $timestamps = false;
}
