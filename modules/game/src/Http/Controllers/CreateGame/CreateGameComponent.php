<?php

namespace Dnw\Game\Http\Controllers\CreateGame;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class CreateGameComponent extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];

    private BusInterface $bus;

    public CreateGameFormViewModel $view;

    public function mount(): void
    {
        $variants = $this->bus->handle(new GetAllVariantsQuery());
        $this->view = CreateGameFormViewModel::fromLaravel($variants);
    }

    public function boot(BusInterface $bus): void
    {
        $this->bus = $bus;
    }

    public function form(Form $form): Form
    {
        $vm = $this->view;
        return $form
            ->schema([
                TextInput::make('title')
                    ->label($vm->name_label)
                    ->helperText('')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function render()
    {
        return view('game::create2');
    }
}
