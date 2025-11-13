<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Models\User;
use Livewire\Component;

class AllVehicles extends Component
{
    public $vehicles = [];
    public $users = [];
    public $vehicleLocations = [];

    public function mount()
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $this->loadData();
    }

    public function loadData()
    {
        // Get all vehicles with their users
        $this->vehicles = Vehicle::with('user')
            ->orderBy('data_updated_at', 'desc')
            ->get();

        // Get user statistics
        $this->users = User::whereHas('vehicles')
            ->withCount('vehicles')
            ->get();

        // Prepare vehicle locations for map
        $this->vehicleLocations = $this->vehicles
            ->filter(function($vehicle) {
                return $vehicle->latitude && $vehicle->longitude && $vehicle->user;
            })
            ->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'latitude' => $vehicle->latitude,
                    'longitude' => $vehicle->longitude,
                    'battery_level' => $vehicle->battery_level,
                    'charging_status' => $vehicle->charging_status,
                    'owner' => $vehicle->user->name,
                ];
            })
            ->values();
    }

    public function render()
    {
        return view('livewire.admin.all-vehicles');
    }
}
