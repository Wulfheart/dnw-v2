<?php

use App\Web\Game\GamePanel\GamePanelPlayersJoiningViewModel;

/** @var GamePanelPlayersJoiningViewModel $vm */
?>

<x-layout>
    <x-game.game-header :info="$vm->gameInfo"/>
    <div class="content content-follow-on">
        <div id="mapstore">
            <img src="{{ $vm->mapLink }}">
        </div>
    </div>
    {{ $vm->gameInfo->name }} has been created.
</x-layout>
