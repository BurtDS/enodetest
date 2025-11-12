<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Services\EnodeService;
use Illuminate\Console\Command;

class SyncVehiclesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicles:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all vehicles data from Enode and track charging sessions/drives';

    /**
     * Execute the console command.
     */
    public function handle(EnodeService $enodeService)
    {
        $this->info('Starting vehicle data sync...');

        $vehicles = Vehicle::all();

        if ($vehicles->isEmpty()) {
            $this->warn('No vehicles found to sync.');
            return Command::SUCCESS;
        }

        $synced = 0;
        $failed = 0;

        foreach ($vehicles as $vehicle) {
            $this->info("Syncing {$vehicle->make} {$vehicle->model} (ID: {$vehicle->id})...");

            try {
                if ($enodeService->syncVehicleData($vehicle)) {
                    $synced++;
                    $this->info("  ✓ Synced successfully");
                } else {
                    $failed++;
                    $this->error("  ✗ Failed to sync");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("  ✗ Error: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Sync completed: {$synced} successful, {$failed} failed");

        return Command::SUCCESS;
    }
}
