<?php

use App\Web\Game\CreateGame\CreateGameFormViewModel;

/** @var CreateGameFormViewModel $vm */
?>
<x-layout>
    <x-display.header :title="$vm->create_game_title" :description="$vm->create_game_description"/>
    @if($vm->canParticipateInAnotherGame)
        <div>
            <div class="content content-follow-on">
                {!! $vm->form->render() !!}
            </div>
        </div>
    @else
        <div>
                <div class="content content-follow-on">
                    <p>You are already participating in a game. You cannot participate in another game at the same time.</p>
                </div>
        </div>
    @endif

</x-layout>
