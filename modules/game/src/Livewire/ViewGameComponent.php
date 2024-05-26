<?php

namespace Dnw\Game\Livewire;

use Dnw\Foundation\Bus\BusInterface;
use Livewire\Component;

class ViewGameComponent extends Component
{
    private BusInterface $bus;

    public function boot(
        BusInterface $bus
    ): void {
        $this->bus = $bus;
    }

    public function mount(string $id): void
    {
    }
}
