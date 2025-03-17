<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('game_variants', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('name');
            $table->string('description');
            $table->unsignedInteger('default_supply_centers_to_win_count');
            $table->unsignedInteger('total_supply_center_count');
            $table->timestamps();
        });

        Schema::create('game_variant_powers', function (Blueprint $table) {
            $table->string('key');
            $table->string('variant_key')->index();
            $table->string('name');
            $table->string('color');
            $table->timestamps();

            $table->foreign('variant_key')->references('key')->on('game_variants');
            $table->unique(['key', 'variant_key']);

        });
    }
};
