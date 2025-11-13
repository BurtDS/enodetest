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
            // Root level vehicle data
            $table->boolean('is_reachable')->nullable()->after('vin');
            $table->timestamp('last_seen')->nullable()->after('is_reachable');

            // Information
            $table->string('display_name')->nullable()->after('vin');

            // Charge state - Range/Autonomy
            $table->decimal('range', 10, 2)->nullable()->after('battery_capacity');
            $table->decimal('previous_range', 10, 2)->nullable()->after('range');
            $table->string('range_unit', 10)->default('km')->after('previous_range');

            // Charge state - Charging details
            $table->decimal('charge_rate', 10, 2)->nullable()->after('range_unit');
            $table->integer('charge_time_remaining')->nullable()->after('charge_rate')->comment('Minutes');
            $table->boolean('is_fully_charged')->nullable()->after('charge_time_remaining');
            $table->boolean('is_plugged_in')->nullable()->after('is_fully_charged');
            $table->integer('charge_limit')->nullable()->after('is_plugged_in')->comment('Percentage');
            $table->string('power_delivery_state')->nullable()->after('charge_limit');
            $table->integer('max_current')->nullable()->after('power_delivery_state')->comment('Amperes');
            $table->string('plugged_in_charger_id')->nullable()->after('max_current');
            $table->timestamp('charge_state_updated_at')->nullable()->after('plugged_in_charger_id');

            // Smart charging policy
            $table->boolean('smart_charging_enabled')->default(false)->after('charge_state_updated_at');
            $table->timestamp('smart_charging_deadline')->nullable()->after('smart_charging_enabled');
            $table->integer('smart_charging_minimum_charge_limit')->nullable()->after('smart_charging_deadline');

            // Capabilities (store as JSON)
            $table->json('capabilities')->nullable()->after('smart_charging_minimum_charge_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'is_reachable',
                'last_seen',
                'display_name',
                'range',
                'previous_range',
                'range_unit',
                'charge_rate',
                'charge_time_remaining',
                'is_fully_charged',
                'is_plugged_in',
                'charge_limit',
                'power_delivery_state',
                'max_current',
                'plugged_in_charger_id',
                'charge_state_updated_at',
                'smart_charging_enabled',
                'smart_charging_deadline',
                'smart_charging_minimum_charge_limit',
                'capabilities',
            ]);
        });
    }
};
