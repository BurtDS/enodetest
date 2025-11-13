<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overview of all vehicles and their current status</p>
        </div>
    </div>

    {{-- All Vehicles Datatable --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden">
        <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">All Vehicles ({{ count($vehicles) }})</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Real-time overview of vehicle status</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-neutral-900">
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Vehicle & Owner</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Battery & Range</th>
                        <th class="text-right py-2 px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Odometer</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Updated</th>
                        <th class="text-center py-2 px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-900/50 transition-colors">
                            {{-- Vehicle & Owner --}}
                            <td class="py-2 px-3">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ $vehicle->make }} {{ $vehicle->model }}</span>
                                        @if($vehicle->year)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $vehicle->year }}</span>
                                        @endif
                                        @if($vehicle->vendor)
                                            <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ ucfirst($vehicle->vendor) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $vehicle->user->name }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $vehicle->user->email }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Battery & Range --}}
                            <td class="py-2 px-3">
                                <div class="flex flex-col gap-2">
                                    {{-- Battery --}}
                                    @if($vehicle->battery_level !== null)
                                        <div class="min-w-[120px]">
                                            <div class="flex items-center justify-between mb-0.5">
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Battery</span>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-xs font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->battery_level, 0) }}%</span>
                                                    @if($vehicle->charging_status === 'charging')
                                                        <svg class="w-3 h-3 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M11 3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V5a1 1 0 10-2 0v3.5a.5.5 0 01-1 0V3a1 1 0 10-2 0v8a7 7 0 1014 0V3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V8a1 1 0 10-2 0v.5a.5.5 0 01-1 0V3z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                                                @php
                                                    $batteryColor = match(true) {
                                                        $vehicle->battery_level >= 80 => 'bg-green-500',
                                                        $vehicle->battery_level >= 50 => 'bg-blue-500',
                                                        $vehicle->battery_level >= 20 => 'bg-yellow-500',
                                                        default => 'bg-red-500'
                                                    };
                                                @endphp
                                                <div class="{{ $batteryColor }} h-1.5 rounded-full" style="width: {{ $vehicle->battery_level }}%"></div>
                                            </div>
                                            @if($vehicle->previous_battery_level && $vehicle->previous_battery_level != $vehicle->battery_level)
                                                @php
                                                    $batteryDiff = $vehicle->battery_level - $vehicle->previous_battery_level;
                                                @endphp
                                                <span class="text-xs {{ $batteryDiff > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $batteryDiff > 0 ? '+' : '' }}{{ number_format($batteryDiff, 0) }}%
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">Battery: N/A</span>
                                    @endif

                                    {{-- Range --}}
                                    @if($vehicle->range !== null)
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">Range</span>
                                            <div class="flex flex-col items-end">
                                                <span class="text-xs font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                                    {{ number_format($vehicle->range, 0) }} {{ $vehicle->range_unit }}
                                                </span>
                                                @if($vehicle->previous_range && $vehicle->previous_range != $vehicle->range)
                                                    @php
                                                        $rangeDiff = $vehicle->range - $vehicle->previous_range;
                                                    @endphp
                                                    <span class="text-xs {{ $rangeDiff > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                        {{ $rangeDiff > 0 ? '+' : '' }}{{ number_format($rangeDiff, 0) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">Range: N/A</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Odometer --}}
                            <td class="py-2 px-3 text-right">
                                @if($vehicle->odometer !== null)
                                    <div class="flex flex-col items-end">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ number_format($vehicle->odometer, 0) }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $vehicle->odometer_unit ?? 'km' }}</span>
                                        @if($vehicle->previous_odometer && $vehicle->previous_odometer < $vehicle->odometer)
                                            @php
                                                $odometerDiff = $vehicle->odometer - $vehicle->previous_odometer;
                                            @endphp
                                            <span class="text-xs text-green-600 dark:text-green-400">
                                                +{{ number_format($odometerDiff, 0) }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">N/A</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-2 px-3">
                                <div class="flex flex-col gap-1">
                                    @if($vehicle->charging_status === 'charging')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 w-fit">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                                            Charging
                                        </span>
                                        @if($vehicle->charge_rate)
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ number_format($vehicle->charge_rate, 1) }} kW</span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 w-fit">
                                            Idle
                                        </span>
                                    @endif
                                    <div class="flex flex-wrap gap-1">
                                        @if($vehicle->is_plugged_in)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200">Plugged</span>
                                        @endif
                                        @if($vehicle->smart_charging_enabled)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200">Smart</span>
                                        @endif
                                        @if($vehicle->is_reachable === false)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200">Offline</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Updated --}}
                            <td class="py-2 px-3">
                                @if($vehicle->data_updated_at)
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-900 dark:text-white font-medium">{{ $vehicle->data_updated_at->format('M d') }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $vehicle->data_updated_at->format('g:i A') }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $vehicle->data_updated_at->diffForHumans(null, true) }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Never</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-2 px-3 text-center">
                                <a href="{{ route('vehicles.detail', $vehicle) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400 font-medium">No vehicles found</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Vehicles will appear here once users connect their accounts</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid gap-6 md:grid-cols-4">
        {{-- Total Vehicles --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Vehicles</span>
                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ count($vehicles) }}</p>
        </div>

        {{-- Charging Now --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Currently Charging</span>
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M11 3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V5a1 1 0 10-2 0v3.5a.5.5 0 01-1 0V3a1 1 0 10-2 0v8a7 7 0 1014 0V3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V8a1 1 0 10-2 0v.5a.5.5 0 01-1 0V3z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $vehicles->where('charging_status', 'charging')->count() }}
            </p>
        </div>

        {{-- Average Battery --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Battery</span>
                <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm2 0v8h12V6H4zm1 2h10v4H5V8z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $vehicles->whereNotNull('battery_level')->count() > 0 ? number_format($vehicles->whereNotNull('battery_level')->avg('battery_level'), 0) : 0 }}%
            </p>
        </div>

        {{-- Total Users --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</span>
                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ count($users) }}</p>
        </div>
    </div>

    {{-- Vehicle Locations Overview Map --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden">
        <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Vehicle Locations Overview</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Real-time map showing all vehicle locations</p>
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-medium">{{ count($vehicleLocations) }}</span> of <span class="font-medium">{{ count($vehicles) }}</span> vehicles with location data
                </div>
            </div>
            @if(count($vehicleLocations) < count($vehicles))
                <div class="mt-3 text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-3 py-2 rounded-lg">
                    Note: Some vehicle manufacturers (e.g., Peugeot) may not provide location data through their API.
                </div>
            @endif
        </div>
        <div id="admin-overview-map" class="w-full h-[600px]" style="height: 600px; width: 100%;" wire:ignore></div>
    </div>
</div>

@assets
<script>
let overviewMap = null;

document.addEventListener('livewire:navigated', () => {
    initOverviewMap();
});

function initOverviewMap() {
    const mapElement = document.getElementById('admin-overview-map');

    if (!mapElement || typeof L === 'undefined') {
        return;
    }

    // Remove existing map if it exists
    if (overviewMap) {
        overviewMap.remove();
        overviewMap = null;
    }

    // Check if container already has a map instance
    if (mapElement._leaflet_id) {
        mapElement._leaflet_id = undefined;
    }

    overviewMap = L.map('admin-overview-map').setView([50.8503, 4.3517], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(overviewMap);

    // Force map to calculate size after a brief delay
    setTimeout(() => {
        overviewMap.invalidateSize();
    }, 100);

    const vehicles = @json($vehicleLocations);
    const bounds = [];

    vehicles.forEach(vehicle => {
        if (vehicle.latitude && vehicle.longitude) {
            bounds.push([vehicle.latitude, vehicle.longitude]);

            const marker = L.marker([vehicle.latitude, vehicle.longitude]).addTo(overviewMap);
            marker.bindPopup(`<b>${vehicle.make} ${vehicle.model}</b><br>Owner: ${vehicle.owner}`);
        }
    });

    if (bounds.length > 0) {
        overviewMap.fitBounds(bounds, { padding: [50, 50] });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOverviewMap);
} else {
    initOverviewMap();
}
</script>
@endassets
