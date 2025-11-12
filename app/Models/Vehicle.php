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
        'battery_level',
        'battery_capacity',
        'previous_battery_level',
        'charging_status',
        'previous_charging_status',
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
            'battery_level' => 'decimal:2',
            'battery_capacity' => 'decimal:2',
            'previous_battery_level' => 'decimal:2',
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
