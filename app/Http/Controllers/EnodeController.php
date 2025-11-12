<?php

namespace App\Http\Controllers;

use App\Models\EnodeToken;
use App\Models\Vehicle;
use App\Services\EnodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EnodeController extends Controller
{
    public function __construct(
        private EnodeService $enodeService
    ) {}

    /**
     * Redirect user to Enode Link to authorize their vehicles
     */
    public function redirect()
    {
        $user = Auth::user();
        $state = Str::random(40);

        // Create a unique user ID for Enode (use your app's user ID)
        $enodeUserId = 'user_' . $user->id;

        // Store both state and the Enode user ID in session
        session([
            'enode_state' => $state,
            'enode_user_id' => $enodeUserId,
        ]);

        $linkUrl = $this->enodeService->createLinkSession($enodeUserId, $state);

        if (!$linkUrl) {
            return redirect()->route('dashboard')
                ->with('error', 'Failed to create Enode Link session. Please check your API credentials.');
        }

        return redirect($linkUrl);
    }

    /**
     * Handle the callback from Enode Link
     */
    public function callback(Request $request)
    {
        \Log::info('Enode callback received', $request->all());

        // Verify linkState to prevent CSRF
        if ($request->linkState !== session('enode_state')) {
            \Log::error('Invalid linkState', [
                'received' => $request->linkState,
                'expected' => session('enode_state'),
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Invalid state parameter. Please try linking your vehicle again.');
        }

        // Get the Enode user ID from session (we stored it when initiating the link)
        $enodeUserId = session('enode_user_id');

        session()->forget(['enode_state', 'enode_user_id']);

        // Handle errors
        if ($request->has('error')) {
            \Log::error('Enode error in callback', ['error' => $request->error, 'description' => $request->error_description]);
            return redirect()->route('dashboard')
                ->with('error', 'Authorization failed: ' . ($request->error_description ?? $request->error));
        }

        if (!$enodeUserId) {
            \Log::error('No Enode user ID in session');
            return redirect()->route('dashboard')
                ->with('error', 'Session expired. Please try connecting again.');
        }

        $user = Auth::user();

        \Log::info('Processing Enode user', ['enode_user_id' => $enodeUserId, 'app_user_id' => $user->id]);

        // Note: With the Link flow, we don't get access/refresh tokens in the callback.
        // Instead, we use our API credentials to access user data.
        // Store the Enode user ID for this user
        EnodeToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'enode_user_id' => $enodeUserId,
                'access_token' => 'api_credentials', // We'll use API credentials instead
                'refresh_token' => 'api_credentials',
                'expires_at' => now()->addYears(10), // Long expiry since we use API credentials
            ]
        );

        \Log::info('Enode token saved');

        // Fetch and store user's vehicles
        $vehiclesData = $this->enodeService->getUserVehicles($user);

        \Log::info('Vehicles data fetched', ['data' => $vehiclesData]);

        if ($vehiclesData && isset($vehiclesData['data'])) {
            foreach ($vehiclesData['data'] as $vehicleData) {
                \Log::info('Creating vehicle', ['vehicle_data' => $vehicleData]);

                // Extract all data from the response
                $information = $vehicleData['information'] ?? [];
                $chargeState = $vehicleData['chargeState'] ?? [];
                $location = $vehicleData['location'] ?? [];
                $odometer = $vehicleData['odometer'] ?? [];

                $vehicle = Vehicle::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'enode_vehicle_id' => $vehicleData['id'],
                    ],
                    [
                        'enode_user_id' => $enodeUserId,
                        'vendor' => $vehicleData['vendor'] ?? null,
                        'make' => $information['brand'] ?? null,
                        'model' => $information['model'] ?? null,
                        'year' => $information['year'] ?? null,
                        'vin' => $information['vin'] ?? null,
                        'battery_level' => $chargeState['batteryLevel'] ?? null,
                        'battery_capacity' => $chargeState['batteryCapacity'] ?? null,
                        'charging_status' => ($chargeState['isCharging'] ?? false) ? 'charging' : 'not_charging',
                        'latitude' => $location['latitude'] ?? null,
                        'longitude' => $location['longitude'] ?? null,
                        'location_updated_at' => isset($location['lastUpdated']) ? now()->parse($location['lastUpdated']) : null,
                        'odometer' => $odometer['distance'] ?? null,
                        'odometer_unit' => 'km',
                        'data_updated_at' => now(),
                    ]
                );

                \Log::info('Vehicle created with all data', ['vehicle_id' => $vehicle->id]);
            }
        } else {
            \Log::error('No vehicles data or invalid format', ['response' => $vehiclesData]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Your vehicle(s) have been successfully linked!');
    }

    /**
     * Disconnect Enode account and remove all vehicles
     */
    public function disconnect()
    {
        $user = Auth::user();

        $user->vehicles()->delete();
        $user->enodeToken()->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Your Enode account has been disconnected.');
    }
}
