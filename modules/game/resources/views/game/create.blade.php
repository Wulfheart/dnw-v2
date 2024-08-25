<?php
/** @var \Dnw\Game\ViewModel\CreateGame\CreateGameFormViewModel $vm */
?>
<x-layout>
    <x-display.header :title="$vm->create_game_title" :description="$vm->create_game_description" />
    <div>
        <div class="content content-follow-on">
            <form action="{{ $vm->endpoint }}" method="POST" class="web">
                @csrf
                <x-input :label="" name="gameName">
                    <input type="text" name="gameName" value="{{ $vm->game_name }}">
            </form>
        </div>
    </div>
</x-layout>
