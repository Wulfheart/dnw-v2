<?php
/** @var \Dnw\Game\Http\Controllers\CreateGame\CreateGameFormViewModel $view */
?>

<x-layout.registered>
    <div class="content-bare content-board-header content-title-header">
        <div class="pageTitle">{{ $view->create_game_title }}</div>
        <div class="pageDescription">{{ $view->create_game_description }}</div>
    </div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>

    <div class="content content-follow-on">
        <div class="gameCreateShow">
            <form method="POST" action="{{ $view->create_game_url }}">
                @csrf
                <p>
                    <strong>{{ $view->name_label }}:</strong>
                    <br>
                    <input class="gameCreate" type="text" name="{{ $view->name_form_key }}">
                </p>
                <p>
                    <strong>{{ $view->phase_length_in_minutes_label }}:</strong>
                    <br>
                    <select class="gameCreate" name="{{ $view->phase_length_in_minutes_form_key }}">
                        @foreach ($view->phase_length_in_minutes_options as $option)
                            <option value="{{ $option->value }}" @selected(old($view->phase_length_in_minutes_form_key, $option->selected))>
                                {{ $option->label }}
                            </option>
                        @endforeach
                    </select>
                </p>
                <p>
                    <strong>{{ $view->variant_id_label }}:</strong>
                    <br>
                    <select class="gameCreate" name="{{ $view->variant_id_form_key }}">
                        @foreach ($view->variant_id_options as $option)
                            <option value="{{ $option->value }}" @selected(old($view->variant_id_form_key, $option->selected))>
                                {{ $option->name }}
                            </option>
                        @endforeach
                    </select>
                </p>

                <h3>{{ $view->advanced_settings_title }}</h3>
                <p>
                    <strong>{{ $view->join_length_in_days_label }}:</strong>
                    <br>
                    <input type="number" class="gameCreate" min="4" max="200"
                        name="{{ $view->join_length_in_days_form_key }}" value="{{ $view->join_length_in_days_default_value }}" size="4">
                    <br>
                    <br>
                    <select class="gameCreate" name="{{ $view->start_when_ready_form_key }}">
                        @foreach ($view->start_when_ready_options as $option)
                            <option value="{{ $option->value }}" @selected(old($view->start_when_ready_form_key, $option->selected))>
                                {{ $option->label }}
                            </option>
                        @endforeach
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
