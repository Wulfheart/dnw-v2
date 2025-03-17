<?php

use App\Web\Game\GamePanel\GamePanelCreatedViewModel;

/** @var GamePanelCreatedViewModel $vm */
?>

<x-layout>
    <x-game.game-header :info="$vm->gameInfo"></x-game.game-header>
    {{ __('game.panel.created', ['name' => $vm->gameInfo->name] }}
</x-layout>
