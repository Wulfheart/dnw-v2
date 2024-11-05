<?php

use App\Web\Game\GamePanel\GamePanelCreatedViewModel;

/** @var GamePanelCreatedViewModel $vm */
?>

<x-layout>
    <x-game::game-header :info="$vm->gameInfo"></x-game::game-header>
    {{ $vm->gameInfo->name }} has been created.
</x-layout>
