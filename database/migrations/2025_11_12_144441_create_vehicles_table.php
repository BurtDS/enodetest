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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('enode_vehicle_id')->unique();
            $table->string('enode_user_id');
            $table->string('vendor')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->string('vin')->nullable();

            // Latest vehicle data (cached from Enode API)
            $table->decimal('battery_level', 5, 2)->nullable();
            $table->decimal('battery_capacity', 8, 2)->nullable();
            $table->string('charging_status')->nullable();
            $table->decimal('odometer', 10, 2)->nullable();
            $table->string('odometer_unit')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->timestamp('data_updated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
