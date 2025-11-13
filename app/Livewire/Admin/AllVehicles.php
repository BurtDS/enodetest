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
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.all-vehicles');
    }
}
