<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('game_games', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('current_state');
            $table->unsignedInteger('adjudication_timing_phase_length');
            $table->json('adjudication_timing_no_adjudication_weekdays');
            $table->timestamp('game_start_timing_start_of_join_phase');
            $table->unsignedInteger('game_start_timing_join_length');
            $table->boolean('game_start_timing_start_when_ready');
            $table->boolean('random_power_assignments');
            $table->ulid('variant_data_variant_id');
            $table->json('variant_data_variant_power_ids');
            $table->unsignedInteger('variant_data_default_supply_centers_to_win_count');
            $table->unsignedInteger('version');
            $table->timestamps();
        });

        Schema::create('game_powers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('player_id')->nullable();
            $table->ulid('game_id');
            $table->ulid('variant_power_id');
            $table->timestamps();
        });

        Schema::create('game_phases', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('game_id');
            $table->unsignedInteger('ordinal_number');
            $table->string('type');
            $table->dateTime('adjudication_time')->nullable();
            $table->timestamps();
        });

        Schema::create('game_phase_power_data', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('phase_id');
            $table->ulid('power_id');
            $table->boolean('orders_needed');
            $table->boolean('marked_as_ready');
            $table->boolean('is_winner');
            $table->unsignedInteger('supply_center_count');
            $table->unsignedInteger('unit_count');
            $table->json('order_collection')->nullable();
            $table->json('applied_orders')->nullable();
            $table->timestamps();
        });
    }
};
