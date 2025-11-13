<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'user_id',
        'enode_vehicle_id',
        'enode_user_id',
        'vendor',
        'make',
        'model',
        'year',
        'vin',
        'display_name',
        'is_reachable',
        'last_seen',
        'battery_level',
        'battery_capacity',
        'previous_battery_level',
        'range',
        'previous_range',
        'range_unit',
        'charging_status',
        'previous_charging_status',
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
        'odometer',
        'odometer_unit',
        'previous_odometer',
        'latitude',
        'longitude',
        'previous_latitude',
        'previous_longitude',
        'location_updated_at',
        'data_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_reachable' => 'boolean',
            'last_seen' => 'datetime',
            'battery_level' => 'decimal:2',
            'battery_capacity' => 'decimal:2',
            'previous_battery_level' => 'decimal:2',
            'range' => 'decimal:2',
            'previous_range' => 'decimal:2',
            'charge_rate' => 'decimal:2',
            'charge_time_remaining' => 'integer',
            'is_fully_charged' => 'boolean',
            'is_plugged_in' => 'boolean',
            'charge_limit' => 'integer',
            'max_current' => 'integer',
            'charge_state_updated_at' => 'datetime',
            'smart_charging_enabled' => 'boolean',
            'smart_charging_deadline' => 'datetime',
            'smart_charging_minimum_charge_limit' => 'integer',
            'capabilities' => 'array',
            'odometer' => 'decimal:2',
            'previous_odometer' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'previous_latitude' => 'decimal:8',
            'previous_longitude' => 'decimal:8',
            'location_updated_at' => 'datetime',
            'data_updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chargingSessions(): HasMany
    {
        return $this->hasMany(ChargingSession::class);
    }

    public function drives(): HasMany
    {
        return $this->hasMany(Drive::class);
    }
}
