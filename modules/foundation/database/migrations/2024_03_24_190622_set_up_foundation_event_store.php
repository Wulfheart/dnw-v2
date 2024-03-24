<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
	public function up(): void
	{
		Schema::create('foundation_event_store', function(Blueprint $table) {
            $table->ulid('id');
            $table->string('fqdn');
            $table->text('payload');
            $table->dateTime('recorded_at');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('foundation_event_store');
	}
};
