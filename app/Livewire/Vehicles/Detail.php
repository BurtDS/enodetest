<?php

namespace App\Livewire\Vehicles;

use App\Models\Vehicle;
use App\Services\EnodeService;
use Livewire\Component;

class Detail extends Component
{
    public Vehicle $vehicle;
    public $chargingSessions = [];
    public $drives = [];
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
        // Load last 5 charging sessions
        $this->chargingSessions = $this->vehicle->chargingSessions()
            ->orderBy('started_at', 'desc')
            ->limit(5)
            ->get();

        // Load last 5 drives
        $this->drives = $this->vehicle->drives()
            ->orderBy('started_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate statistics
        $this->calculateStatistics();
    }

    public function calculateStatistics()
    {
        // Last 30 days statistics
        $startDate = now()->subDays(30);

        // Driving statistics
        $recentDrives = $this->vehicle->drives()
            ->where('started_at', '>=', $startDate)
            ->get();

        $totalDistance = $recentDrives->sum('distance');
        $totalEnergyUsed = $recentDrives->sum('energy_used');
        $averageEfficiency = $totalEnergyUsed > 0 ? $totalDistance / $totalEnergyUsed : 0;

        // Charging statistics
        $recentCharging = $this->vehicle->chargingSessions()
            ->where('started_at', '>=', $startDate)
            ->get();

        $totalEnergyAdded = $recentCharging->sum('energy_added');
        $totalChargingSessions = $recentCharging->count();
        $averageSessionDuration = $recentCharging->avg('duration_minutes');

        // Monthly breakdown (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthDrives = $this->vehicle->drives()
                ->whereBetween('started_at', [$monthStart, $monthEnd])
                ->get();

            $monthCharging = $this->vehicle->chargingSessions()
                ->whereBetween('started_at', [$monthStart, $monthEnd])
                ->get();

            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'distance' => $monthDrives->sum('distance'),
                'drives' => $monthDrives->count(),
                'energy_used' => $monthDrives->sum('energy_used'),
                'energy_added' => $monthCharging->sum('energy_added'),
                'charging_sessions' => $monthCharging->count(),
            ];
        }

        $this->statistics = [
            'total_distance_30d' => round($totalDistance, 1),
            'total_energy_used_30d' => round($totalEnergyUsed, 1),
            'average_efficiency' => round($averageEfficiency, 2),
            'total_energy_added_30d' => round($totalEnergyAdded, 1),
            'total_charging_sessions_30d' => $totalChargingSessions,
            'average_session_duration' => round($averageSessionDuration, 0),
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
