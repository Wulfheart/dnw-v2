<x-layout.registered>
    <div class="content-bare content-board-header content-title-header">
        <div class="pageTitle">Create a new game</div>
        <div class="pageDescription">Start a new game of Diplomacy that other players can join.</div>
    </div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>

    <div class="content content-follow-on">
        <div class="gameCreateShow">
            <form method="POST" action="{{ route('game.store') }}">
                <h3>Basic settings</h3>
                @csrf
                <p>
                    <strong>Name:</strong>
                    <br>
                    <input class="gameCreate" type="text" name="name">
                </p>
                <p>
                    <strong>Phase Length:</strong>
                    <br>
                    <select class="gameCreate" name="phase_length_in_minutes">
                        <option value="5">5 minutes</option>
                        <option value="10">10 minutes</option>
                        <option value="1440" selected>1 day</option>
                    </select>
                </p>
                <p>
                    <strong>Variante:</strong>
                    <br>
                    <select class="gameCreate" name="variant_id">
                        <option value="01HY5Y27GEEDVQ2B3VJK7SM7AW">Standard</option>
                        <option value="01HY5Y2JYRQZRWQPSFNPW3ZHSB">1900</option>
                    </select>
                </p>

                <h3>Advanced Settings</h3>
                <p>
                    <strong>Time to fill game:</strong>
                    <br>
                    <select class="gameCreate" name="join_length_in_days">
                        <option value="1">1 day</option>
                        <option value="2">2 days</option>
                        <option value="3">3 days</option>
                        <option value="7" selected>7 days</option>
                    </select>
                </p>
                <p>
                    <strong>Anonymous orders:</strong>
                    <br>
                    <select class="gameCreate" name="is_anonymous">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </p>
                <p>
                    <strong>Random power assignments:</strong>
                    <br>
                    <select class="gameCreate" name="random_power_assignments">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </p>
                <p>
                    <strong>Ranking:</strong>
                    <br>
                    <select class="gameCreate" name="is_ranked">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </p>
                <p>
                    <strong>Start when ready:</strong>
                    <br>
                    <select class="gameCreate" name="start_when_ready">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </p>
                <div class="hr"></div>
                <p class="notice">
                    <input class="green-Submit" type="submit" value="Create">
                </p>
            </form>
        </div>
    </div>

</x-layout.registered>
