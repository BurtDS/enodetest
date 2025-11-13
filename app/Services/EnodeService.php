<?php

namespace App\Services;

use App\Models\ChargingSession;
use App\Models\Drive;
use App\Models\EnodeToken;
use App\Models\User;
use App\Models\Vehicle;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class EnodeService
{
    private Client $client;
    private string $clientId;
    private string $clientSecret;
    private string $apiUrl;
    private string $oauthUrl;
    private string $redirectUri;
    private string $linkUiUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->clientId = config('enode.client_id');
        $this->clientSecret = config('enode.client_secret');
        $this->apiUrl = config('enode.api_url');
        $this->oauthUrl = config('enode.oauth_url');
        $this->redirectUri = config('enode.redirect_uri');
        $this->linkUiUrl = config('enode.link_ui_url');
    }

    /**
     * Get an API access token using client credentials
     */
    private function getApiAccessToken(): ?string
    {
        try {
            $response = $this->client->post("{$this->oauthUrl}/oauth2/token", [
                'auth' => [$this->clientId, $this->clientSecret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'] ?? null;
        } catch (GuzzleException $e) {
            Log::error('Failed to get Enode API access token', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a Link session and get the Link URL
     */
    public function createLinkSession(string $userId, string $state): ?string
    {
        $apiToken = $this->getApiAccessToken();

        if (!$apiToken) {
            return null;
        }

        // Append state to redirect URI as query parameter for CSRF protection
        $redirectUriWithState = $this->redirectUri . '?linkState=' . urlencode($state);

        try {
            $response = $this->client->post("{$this->apiUrl}/users/{$userId}/link", [
                'headers' => [
                    'Authorization' => "Bearer {$apiToken}",
                    'Accept' => 'application/json',
                    'enode-api-version' => '2024-01-01',
                ],
                'json' => [
                    'vendorType' => 'vehicle',
                    'scopes' => ['vehicle:read:data', 'vehicle:read:location'],
                    'language' => 'en-US',
                    'redirectUri' => $redirectUriWithState,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['linkUrl'] ?? null;
        } catch (GuzzleException $e) {
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body';
            Log::error('Failed to create Enode Link session', [
                'error' => $e->getMessage(),
                'response' => $responseBody,
                'userId' => $userId,
            ]);
            return null;
        }
    }

    /**
     * Exchange authorization code for access token
     */
    public function exchangeCode(string $code): ?array
    {
        try {
            $response = $this->client->post("{$this->oauthUrl}/oauth2/token", [
                'json' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri' => $this->redirectUri,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Enode token exchange failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Refresh an expired access token
     */
    public function refreshToken(EnodeToken $token): ?array
    {
        try {
            $response = $this->client->post("{$this->oauthUrl}/oauth2/token", [
                'json' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $token->refresh_token,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Enode token refresh failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get a valid API access token (uses client credentials, not user tokens)
     */
    private function getValidToken(User $user): ?string
    {
        // With the Link flow, we use API credentials to access user data
        return $this->getApiAccessToken();
    }

    /**
     * Fetch all vehicles for a user from Enode
     */
    public function getUserVehicles(User $user): ?array
    {
        $token = $this->getValidToken($user);

        if (!$token) {
            return null;
        }

        try {
            $response = $this->client->get("{$this->apiUrl}/users/{$user->enodeToken->enode_user_id}/vehicles", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to fetch Enode vehicles', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fetch vehicle information from Enode
     */
    public function getVehicleInfo(User $user, string $vehicleId): ?array
    {
        $token = $this->getValidToken($user);

        if (!$token) {
            return null;
        }

        try {
            $response = $this->client->get("{$this->apiUrl}/vehicles/{$vehicleId}", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to fetch vehicle info', ['vehicle_id' => $vehicleId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fetch vehicle location from Enode
     */
    public function getVehicleLocation(User $user, string $vehicleId): ?array
    {
        $token = $this->getValidToken($user);

        if (!$token) {
            return null;
        }

        try {
            $response = $this->client->get("{$this->apiUrl}/vehicles/{$vehicleId}/location", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to fetch vehicle location', ['vehicle_id' => $vehicleId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fetch vehicle battery/charge state from Enode
     */
    public function getVehicleChargeState(User $user, string $vehicleId): ?array
    {
        $token = $this->getValidToken($user);

        if (!$token) {
            return null;
        }

        try {
            $response = $this->client->get("{$this->apiUrl}/vehicles/{$vehicleId}/charge-state", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to fetch vehicle charge state', ['vehicle_id' => $vehicleId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fetch vehicle odometer reading from Enode
     */
    public function getVehicleOdometer(User $user, string $vehicleId): ?array
    {
        $token = $this->getValidToken($user);

        if (!$token) {
            return null;
        }

        try {
            $response = $this->client->get("{$this->apiUrl}/vehicles/{$vehicleId}/odometer", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to fetch vehicle odometer', ['vehicle_id' => $vehicleId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Sync all vehicle data for a vehicle
     */
    public function syncVehicleData(Vehicle $vehicle): bool
    {
        $user = $vehicle->user;

        // Fetch all vehicles data (which includes all the info we need)
        $vehiclesData = $this->getUserVehicles($user);

        if (!$vehiclesData || !isset($vehiclesData['data'])) {
            Log::error('Failed to fetch vehicles data for sync', ['vehicle_id' => $vehicle->id]);
            return false;
        }

        // Find the specific vehicle in the response
        $vehicleData = collect($vehiclesData['data'])->firstWhere('id', $vehicle->enode_vehicle_id);

        if (!$vehicleData) {
            Log::error('Vehicle not found in response', ['vehicle_id' => $vehicle->enode_vehicle_id]);
            return false;
        }

        // Extract all data from the response
        $information = $vehicleData['information'] ?? [];
        $chargeState = $vehicleData['chargeState'] ?? [];
        $location = $vehicleData['location'] ?? [];
        $odometer = $vehicleData['odometer'] ?? [];
        $smartCharging = $vehicleData['smartChargingPolicy'] ?? [];
        $capabilities = $vehicleData['capabilities'] ?? [];

        // Log if location data is missing
        if (empty($location['latitude']) || empty($location['longitude'])) {
            Log::warning('Location data not available for vehicle', [
                'vehicle_id' => $vehicle->id,
                'enode_vehicle_id' => $vehicle->enode_vehicle_id,
                'vendor' => $vehicleData['vendor'] ?? 'unknown',
                'make' => $information['brand'] ?? 'unknown',
                'location_data' => $location
            ]);
        }

        $newBatteryLevel = $chargeState['batteryLevel'] ?? null;
        $newRange = $chargeState['range'] ?? null;
        $newOdometer = $odometer['distance'] ?? null;
        $newChargingStatus = ($chargeState['isCharging'] ?? false) ? 'charging' : 'not_charging';

        // Track charging sessions and drives before updating
        $this->trackVehicleChanges($vehicle, $newBatteryLevel, $newOdometer, $newChargingStatus, $location);

        // Update vehicle with all data
        $vehicle->update([
            // Root level data
            'vendor' => $vehicleData['vendor'] ?? null,
            'is_reachable' => $vehicleData['isReachable'] ?? null,
            'last_seen' => isset($vehicleData['lastSeen']) ? now()->parse($vehicleData['lastSeen']) : null,

            // Information
            'make' => $information['brand'] ?? null,
            'model' => $information['model'] ?? null,
            'year' => $information['year'] ?? null,
            'vin' => $information['vin'] ?? null,
            'display_name' => $information['displayName'] ?? null,

            // Battery and charge state
            'previous_battery_level' => $vehicle->battery_level,
            'battery_level' => $newBatteryLevel,
            'battery_capacity' => $chargeState['batteryCapacity'] ?? null,
            'previous_range' => $vehicle->range,
            'range' => $newRange,
            'range_unit' => 'km',

            // Charging status and details
            'previous_charging_status' => $vehicle->charging_status,
            'charging_status' => $newChargingStatus,
            'charge_rate' => $chargeState['chargeRate'] ?? null,
            'charge_time_remaining' => $chargeState['chargeTimeRemaining'] ?? null,
            'is_fully_charged' => $chargeState['isFullyCharged'] ?? null,
            'is_plugged_in' => $chargeState['isPluggedIn'] ?? null,
            'charge_limit' => $chargeState['chargeLimit'] ?? null,
            'power_delivery_state' => $chargeState['powerDeliveryState'] ?? null,
            'max_current' => $chargeState['maxCurrent'] ?? null,
            'plugged_in_charger_id' => $chargeState['pluggedInChargerId'] ?? null,
            'charge_state_updated_at' => isset($chargeState['lastUpdated']) ? now()->parse($chargeState['lastUpdated']) : null,

            // Smart charging policy
            'smart_charging_enabled' => $smartCharging['isEnabled'] ?? false,
            'smart_charging_deadline' => isset($smartCharging['deadline']) ? now()->parse($smartCharging['deadline']) : null,
            'smart_charging_minimum_charge_limit' => $smartCharging['minimumChargeLimit'] ?? null,

            // Capabilities (store as JSON)
            'capabilities' => $capabilities,

            // Location
            'previous_latitude' => $vehicle->latitude,
            'previous_longitude' => $vehicle->longitude,
            'latitude' => $location['latitude'] ?? null,
            'longitude' => $location['longitude'] ?? null,
            'location_updated_at' => isset($location['lastUpdated']) ? now()->parse($location['lastUpdated']) : null,

            // Odometer
            'previous_odometer' => $vehicle->odometer,
            'odometer' => $newOdometer,
            'odometer_unit' => 'km',

            // Sync timestamp
            'data_updated_at' => now(),
        ]);

        return true;
    }

    /**
     * Track vehicle state changes and create charging sessions / drives
     */
    private function trackVehicleChanges(Vehicle $vehicle, ?float $newBatteryLevel, ?float $newOdometer, string $newChargingStatus, array $location): void
    {
        // Detect charging sessions
        if ($vehicle->previous_charging_status === 'not_charging' && $newChargingStatus === 'charging') {
            // Charging just started
            ChargingSession::create([
                'vehicle_id' => $vehicle->id,
                'started_at' => now(),
                'start_battery_level' => $vehicle->battery_level,
                'latitude' => $location['latitude'] ?? null,
                'longitude' => $location['longitude'] ?? null,
            ]);
            Log::info('Charging session started', ['vehicle_id' => $vehicle->id]);
        } elseif ($vehicle->previous_charging_status === 'charging' && $newChargingStatus === 'not_charging') {
            // Charging just ended
            $session = $vehicle->chargingSessions()->whereNull('ended_at')->latest('started_at')->first();
            if ($session) {
                $duration = now()->diffInMinutes($session->started_at);
                $batteryGain = $newBatteryLevel - $session->start_battery_level;
                $energyAdded = ($batteryGain / 100) * ($vehicle->battery_capacity ?? 0);

                $session->update([
                    'ended_at' => now(),
                    'end_battery_level' => $newBatteryLevel,
                    'energy_added' => $energyAdded > 0 ? $energyAdded : null,
                    'duration_minutes' => $duration,
                ]);
                Log::info('Charging session ended', ['vehicle_id' => $vehicle->id, 'session_id' => $session->id]);
            }
        }

        // Detect drives
        if ($vehicle->previous_odometer && $newOdometer && $newOdometer > $vehicle->previous_odometer) {
            $distance = $newOdometer - $vehicle->previous_odometer;

            // Only record if distance is reasonable (more than 1km, less than 1000km)
            if ($distance >= 1 && $distance <= 1000) {
                $batteryUsed = $vehicle->previous_battery_level && $newBatteryLevel
                    ? $vehicle->previous_battery_level - $newBatteryLevel
                    : null;

                $energyUsed = $batteryUsed && $vehicle->battery_capacity
                    ? ($batteryUsed / 100) * $vehicle->battery_capacity
                    : null;

                Drive::create([
                    'vehicle_id' => $vehicle->id,
                    'started_at' => now()->subMinutes(30), // Estimate start time
                    'ended_at' => now(),
                    'start_odometer' => $vehicle->previous_odometer,
                    'end_odometer' => $newOdometer,
                    'distance' => $distance,
                    'distance_unit' => 'km',
                    'start_battery_level' => $vehicle->previous_battery_level,
                    'end_battery_level' => $newBatteryLevel,
                    'energy_used' => $energyUsed,
                    'start_latitude' => $vehicle->latitude,
                    'start_longitude' => $vehicle->longitude,
                    'end_latitude' => $location['latitude'] ?? null,
                    'end_longitude' => $location['longitude'] ?? null,
                ]);
                Log::info('Drive recorded', ['vehicle_id' => $vehicle->id, 'distance' => $distance]);
            }
        }
    }

    /**
     * Fetch charging session statistics for a user
     */
    public function getChargingSessionStatistics(User $user, string $vehicleId, ?string $startDate = null, ?string $endDate = null): ?array
    {
        $token = $this->getValidToken($user);

        if (!$token) {
            return null;
        }

        try {
            // Default to last 30 days if no dates provided
            if (!$startDate) {
                $startDate = now()->subDays(30)->toIso8601String();
            }
            if (!$endDate) {
                $endDate = now()->toIso8601String();
            }

            $queryParams = [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'type' => 'vehicle',
                'id' => $vehicleId,
            ];

            $response = $this->client->get("{$this->apiUrl}/users/{$user->enodeToken->enode_user_id}/statistics/charging/sessions", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
                'query' => $queryParams,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Failed to fetch charging session statistics', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
