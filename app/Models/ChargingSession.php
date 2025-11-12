<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChargingSession extends Model
{
    protected $fillable = [
        'vehicle_id',
        'started_at',
        'ended_at',
        'start_battery_level',
        'end_battery_level',
        'energy_added',
        'duration_minutes',
        'location',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'start_battery_level' => 'decimal:2',
        'end_battery_level' => 'decimal:2',
        'energy_added' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getBatteryGainAttribute(): ?float
    {
        if ($this->start_battery_level !== null && $this->end_battery_level !== null) {
            return $this->end_battery_level - $this->start_battery_level;
        }
        return null;
    }
}
