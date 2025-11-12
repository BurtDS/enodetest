<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Models\User;
use Livewire\Component;

class AllVehicles extends Component
{
    public $vehicles = [];
    public $users = [];

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
            ->with('vehicles')
            ->get()
            ->map(function ($user) {
                $totalDistance = $user->vehicles->sum(function ($vehicle) {
                    return $vehicle->drives()->where('started_at', '>=', now()->subDays(30))->sum('distance');
                });

                $totalEnergy = $user->vehicles->sum(function ($vehicle) {
                    return $vehicle->drives()->where('started_at', '>=', now()->subDays(30))->sum('energy_used');
                });

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'vehicles_count' => $user->vehicles_count,
                    'total_distance_30d' => round($totalDistance, 1),
                    'total_energy_30d' => round($totalEnergy, 1),
                ];
            });
    }

    public function render()
    {
        return view('livewire.admin.all-vehicles');
    }
}
