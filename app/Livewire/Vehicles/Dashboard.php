<?php

namespace App\Livewire\Vehicles;

use App\Services\EnodeService;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public $vehicles = [];
    public $hasEnodeConnection = false;
    public $isRefreshing = false;

    public function mount()
    {
        $this->loadVehicles();
    }

    public function loadVehicles()
    {
        $user = auth()->user();
        $this->hasEnodeConnection = $user->hasEnodeConnection();
        $this->vehicles = $user->vehicles()->get();
    }

    public function refreshVehicle($vehicleId)
    {
        $this->isRefreshing = true;

        $vehicle = auth()->user()->vehicles()->find($vehicleId);

        if ($vehicle) {
            $enodeService = new EnodeService();
            $enodeService->syncVehicleData($vehicle);
        }

        $this->loadVehicles();
        $this->isRefreshing = false;

        session()->flash('success', 'Vehicle data refreshed successfully!');
    }

    public function refreshAll()
    {
        $this->isRefreshing = true;

        $enodeService = new EnodeService();

        foreach (auth()->user()->vehicles as $vehicle) {
            $enodeService->syncVehicleData($vehicle);
        }

        $this->loadVehicles();
        $this->isRefreshing = false;

        session()->flash('success', 'All vehicle data refreshed successfully!');
    }

    public function render()
    {
        return view('livewire.vehicles.dashboard');
    }
}
