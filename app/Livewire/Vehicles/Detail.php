<?php

namespace App\Livewire\Vehicles;

use App\Models\Vehicle;
use App\Services\EnodeService;
use Livewire\Component;

class Detail extends Component
{
    public Vehicle $vehicle;
    public $isRefreshing = false;
    public $statistics = [];

    public function mount(Vehicle $vehicle)
    {
        // Ensure the vehicle belongs to the authenticated user or user is admin
        if ($vehicle->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $this->vehicle = $vehicle;
        $this->loadData();
    }

    public function loadData()
    {
        // Calculate statistics
        $this->calculateStatistics();
    }

    public function calculateStatistics()
    {
        // Monthly breakdown (last 6 months) - showing odometer and energy data
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            // Get drives for this month if they exist
            $monthDrives = $this->vehicle->drives()
                ->whereBetween('started_at', [$monthStart, $monthEnd])
                ->get();

            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'distance' => $monthDrives->sum('distance'),
                'energy_used' => $monthDrives->sum('energy_used'),
            ];
        }

        $this->statistics = [
            'monthly_data' => $monthlyData,
        ];
    }

    public function refreshVehicle()
    {
        $this->isRefreshing = true;

        $enodeService = new EnodeService();
        $enodeService->syncVehicleData($this->vehicle);

        $this->vehicle->refresh();
        $this->loadData();
        $this->isRefreshing = false;

        session()->flash('success', 'Vehicle data refreshed successfully!');
    }

    public function render()
    {
        return view('livewire.vehicles.detail');
    }
}
