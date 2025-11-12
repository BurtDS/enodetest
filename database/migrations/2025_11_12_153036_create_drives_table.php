<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->decimal('start_odometer', 10, 2)->nullable();
            $table->decimal('end_odometer', 10, 2)->nullable();
            $table->decimal('distance', 10, 2)->nullable(); // km
            $table->string('distance_unit', 10)->default('km');
            $table->decimal('start_battery_level', 5, 2)->nullable();
            $table->decimal('end_battery_level', 5, 2)->nullable();
            $table->decimal('energy_used', 8, 2)->nullable(); // kWh
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 11, 8)->nullable();
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('end_longitude', 11, 8)->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drives');
    }
};
