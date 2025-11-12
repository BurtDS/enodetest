<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Drive extends Model
{
    protected $fillable = [
        'vehicle_id',
        'started_at',
        'ended_at',
        'start_odometer',
        'end_odometer',
        'distance',
        'distance_unit',
        'start_battery_level',
        'end_battery_level',
        'energy_used',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'start_odometer' => 'decimal:2',
        'end_odometer' => 'decimal:2',
        'distance' => 'decimal:2',
        'start_battery_level' => 'decimal:2',
        'end_battery_level' => 'decimal:2',
        'energy_used' => 'decimal:2',
        'start_latitude' => 'decimal:8',
        'start_longitude' => 'decimal:8',
        'end_latitude' => 'decimal:8',
        'end_longitude' => 'decimal:8',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getBatteryUsedAttribute(): ?float
    {
        if ($this->start_battery_level !== null && $this->end_battery_level !== null) {
            return $this->start_battery_level - $this->end_battery_level;
        }
        return null;
    }

    public function getEfficiencyAttribute(): ?float
    {
        if ($this->distance > 0 && $this->energy_used > 0) {
            return $this->distance / $this->energy_used; // km per kWh
        }
        return null;
    }
}
