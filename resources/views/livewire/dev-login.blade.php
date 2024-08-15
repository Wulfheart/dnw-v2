<div>
    <div class="content-bare content-board-header content-title-header">
        <div class="pageTitle barAlt1">Dev Login</div>
        <div class="pageDescription">This should only be used in development mode.</div>
    </div>
    <div>
        <div class="content content-follow-on">
            <div class="gameCreateShow">
                <form wire:submit="login">
                    <p>
                        <strong>User:</strong>
                        <select class="gameCreate" wire:model="userId">
                            <option value=""></option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
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

</div>
