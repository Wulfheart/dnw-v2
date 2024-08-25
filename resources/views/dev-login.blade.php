<?php

use App\ViewModel\DevLogin\DevLoginViewModel;

/** @var DevLoginViewModel $viewModel */
?>

<x-layout>
    <x-display.header title="Dev-Login" description="This should only be visible in dev" />
    <div>
        <div class="content content-follow-on">
            <form action="{{ $viewModel->endpoint }}" method="POST" class="web">
                @csrf
                <x-input label="User" key="userId">
                    <select name="userId">
                        <option value=""></option>
                        @foreach($viewModel->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </x-input>
                <input type="submit" value="Login">
            </form>
        </div>

    </div>


</x-layout>

