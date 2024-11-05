<?php

use App\Web\Game\CreateGame\CreateGameFormViewModel;

/** @var CreateGameFormViewModel $vm */

?>
<x-layout>
    <x-display.header :title="$vm->create_game_title" :description="$vm->create_game_description"/>
    <div>
        <div class="content content-follow-on">
            {!! $vm->form->render() !!}
        </div>
    </div>
</x-layout>
