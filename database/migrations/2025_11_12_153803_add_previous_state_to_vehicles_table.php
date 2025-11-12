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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('previous_battery_level', 5, 2)->nullable()->after('battery_capacity');
            $table->decimal('previous_odometer', 10, 2)->nullable()->after('odometer_unit');
            $table->string('previous_charging_status')->nullable()->after('charging_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['previous_battery_level', 'previous_odometer', 'previous_charging_status']);
        });
    }
};
