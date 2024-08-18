<?php
    /** @var \App\ViewModel\DevLogin\DevLoginViewModel $viewModel */
?>

<x-layout>
    <div class="content-bare content-board-header content-title-header">
        <div class="pageTitle barAlt1">Dev Login</div>
        <div class="pageDescription">This should only be used in development mode.</div>
    </div>
    <div>
        <div class="content content-follow-on">
            <div class="gameCreateShow">
                <form>
                    <p>
                        <strong>User:</strong>
                        <select class="gameCreate" name="">
                            <option value=""></option>
                            @foreach($viewModel->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </p>
                    <p class="notice">
                        <input class="green-Submit" type="submit" value="Login">
                    </p>
                </form>
            </div>
        </div>

    </div>


</x-layout>

