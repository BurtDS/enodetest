<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Services\EnodeService;
use Illuminate\Console\Command;

class RefreshVehicleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicles:refresh {--all : Refresh all vehicles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh vehicle data from Enode API';

    /**
     * Execute the console command.
     */
    public function handle(EnodeService $enodeService)
    {
        $vehicles = Vehicle::with('user.enodeToken')->get();

        if ($vehicles->isEmpty()) {
            $this->info('No vehicles found to refresh.');
            return Command::SUCCESS;
        }

        $this->info("Refreshing data for {$vehicles->count()} vehicle(s)...");

        $progressBar = $this->output->createProgressBar($vehicles->count());
        $progressBar->start();

        $successCount = 0;
        $failureCount = 0;

        foreach ($vehicles as $vehicle) {
            try {
                $enodeService->syncVehicleData($vehicle);
                $successCount++;
            } catch (\Exception $e) {
                $this->error("\nFailed to refresh vehicle {$vehicle->id}: {$e->getMessage()}");
                $failureCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("Successfully refreshed: {$successCount}");
        if ($failureCount > 0) {
            $this->warn("Failed to refresh: {$failureCount}");
        }

        return Command::SUCCESS;
    }
}
