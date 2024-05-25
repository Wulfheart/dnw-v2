<?php /** @var \Dnw\Game\Http\Controllers\CreateGame\CreateGameFormViewModel $view */?>

<x-container :title="$view->create_game_title">
    <form wire:submit="create">
        {{ $this->form }}
    </form>
</x-container>
