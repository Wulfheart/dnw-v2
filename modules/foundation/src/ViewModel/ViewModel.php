<?php

namespace Dnw\Foundation\ViewModel;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, static>
 */
abstract class ViewModel implements Arrayable {

    /**
     * @return array<string, static>
     */
    public function toArray(): array
    {
        return ['view' => $this];
    }
}
