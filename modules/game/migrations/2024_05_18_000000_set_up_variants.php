<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('game_variants', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('description');
            $table->string('api_name');
            $table->unsignedInteger('default_supply_centers_to_win_count');
            $table->unsignedInteger('total_supply_center_count');
            $table->timestamps();
        });

        Schema::create('game_variant_powers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('variant_id');
            $table->string('name');
            $table->string('api_name');
            $table->string('color');
            $table->timestamps();
        });
    }
};
