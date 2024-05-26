<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * @property-read Form $form
 */
class DevLogin extends Component implements HasForms
{
    use InteractsWithForms;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Actions::make([
                    Action::make('Login')
                        ->label('Login')
                        ->button()
                        ->submit('login'),
                ])->fullWidth(),
            ])
            ->statePath('data');
    }

    public function login(): void
    {
        Auth::loginUsingId($this->form->getState()['user_id']);

        $this->redirect(route('game.create'), true);
    }

    public function render()
    {
        return view('livewire.dev-login');
    }
}
