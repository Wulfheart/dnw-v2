<?php

use App\ViewModel\DevLogin\DevLoginViewModel;

/** @var DevLoginViewModel $viewModel */
?>

<x-layout>
    <div class="content-bare content-board-header content-title-header">
        <div class="pageTitle barAlt1">Dev Login</div>
        <div class="pageDescription">This should only be used in development mode.</div>
    </div>
    <div>
        <div class="content content-follow-on">
            <form action="{{ $viewModel->endpoint }}" method="POST" class="web">
                @csrf
                <x-input label="User" name="userId">
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

