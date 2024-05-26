<?php

namespace Dnw\Game\Livewire;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\State\State;
use Dnw\Game\Core\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQuery;
use Dnw\Game\ViewModel\CreateGame\CreateGameFormViewModel;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Std\Option;

/**
 * @property Form $form
 */
class CreateGameComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public const string KEY_TITLE = 'title';

    public const string KEY_PHASE_LENGTH_IN_MINUTES = 'phase_length_in_minutes';

    public const string KEY_VARIANT_ID = 'variant_id';

    public const string KEY_RANDOM_POWER_ASSIGNMENTS = 'random_power_assignments';

    public const string KEY_SELECTED_POWER_ID = 'selected_power_id';

    public const string KEY_NO_ADJUDICATION_WEEKDAYS = 'no_adjudication_weekdays';

    public const string KEY_ADVANCED_OPTIONS = 'advanced_options';

    public const string KEY_JOIN_LENGTH_IN_DAYS = 'join_length_in_days';

    public const string KEY_START_WHEN_READY = 'start_when_ready';

    /**
     * @var array<array-key, mixed>|null
     */
    public ?array $data = [];

    private BusInterface $bus;

    public CreateGameFormViewModel $view;

    public function mount(): void
    {
        $variants = $this->bus->handle(new GetAllVariantsQuery());
        $this->view = CreateGameFormViewModel::fromLaravel($variants);
        $this->form->fill();

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
                // TODO: Validate if the title is unique
                TextInput::make(self::KEY_TITLE)
                    ->label($vm->name_label)
                    ->helperText('')
                    ->required(),
                Select::make(self::KEY_PHASE_LENGTH_IN_MINUTES)
                    ->label($vm->phase_length_in_minutes_label)
                    ->default($vm->phase_length_in_minutes_options->getSelectedValue())
                    ->options($vm->phase_length_in_minutes_options->getFilamentArray())
                    ->selectablePlaceholder(false)
                    ->required(),
                Select::make(self::KEY_VARIANT_ID)
                    ->label($vm->variant_id_label)
                    ->default($vm->variant_id_options->getSelectedValue())
                    ->options($vm->variant_id_options->getFilamentArray())
                    ->selectablePlaceholder(false)
                    ->required()
                    ->live(),
                Toggle::make(self::KEY_RANDOM_POWER_ASSIGNMENTS)
                    ->label($vm->random_power_assignments_label)
                    ->live()
                    ->default(true),
                Select::make(self::KEY_SELECTED_POWER_ID)
                    ->label($vm->selected_power_id_label)
                    ->options(
                        fn (Get $get) => $vm->variant_id_options
                            ->getVariantInformationOption($get('variant_id'))
                            ->variant_powers
                            ->getFilamentArray()
                    )
                    ->required()
                    ->selectablePlaceholder(false)
                    ->disabled(fn (Get $get): bool => $get('random_power_assignments'))
                    ->visible(fn (Get $get): bool => ! $get('random_power_assignments')),

                // TODO: Validate that there are at most 6 weekdays selected
                CheckboxList::make(self::KEY_NO_ADJUDICATION_WEEKDAYS)
                    ->label($vm->no_adjudication_weekdays_label)
                    ->options($vm->no_adjudication_weekdays_options->getFilamentArray())
                    ->columns(4)
                    ->gridDirection('row'),
                Toggle::make(self::KEY_ADVANCED_OPTIONS)
                    ->label($vm->advanced_settings_title)
                    ->live(),
                Group::make()->schema([
                    TextInput::make(self::KEY_JOIN_LENGTH_IN_DAYS)
                        ->numeric()
                        ->minValue(4)
                        ->maxValue(365)
                        ->label($vm->join_length_in_days_label)
                        ->default($vm->join_length_in_days_default_value)
                        ->required(),
                    Select::make(self::KEY_START_WHEN_READY)
                        ->label('')
                        ->default($vm->start_when_ready_options->getSelectedValue())
                        ->options($vm->start_when_ready_options->getFilamentArray())
                        ->selectablePlaceholder(false)
                        ->required(),
                ])->visible(fn (Get $get): bool => $get('advanced_options')),

                Actions::make([
                    Action::make('Anmelden')
                        ->label('Anmelden')
                        ->button()
                        ->submit('create'),
                ])->fullWidth(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $state = new State($this->form->getState());
        $gameId = Id::generate();

        // This is ensured by the middleware
        /** @var string $id */
        $id = Auth::id();

        $command = new CreateGameCommand(
            $gameId,
            $state->get(self::KEY_TITLE),
            $state->get(self::KEY_PHASE_LENGTH_IN_MINUTES),
            $state->get(self::KEY_JOIN_LENGTH_IN_DAYS, $this->view->join_length_in_days_default_value),
            $state->get(self::KEY_START_WHEN_READY, $this->view->start_when_ready_options->getSelectedValue()),
            Id::fromString($state->get(self::KEY_VARIANT_ID)),
            $state->get(self::KEY_RANDOM_POWER_ASSIGNMENTS),
            Option::fromNullable($state->get(self::KEY_SELECTED_POWER_ID))->mapIntoOption(fn (string $id) => Id::fromString($id)),
            true,
            false,
            $state->get(self::KEY_NO_ADJUDICATION_WEEKDAYS, []),
            Id::fromString($id),
        );

        $this->bus->handle($command);

        $this->redirect('/', true);

    }

    public function render(): View
    {
        return view('game::create');
    }
}
