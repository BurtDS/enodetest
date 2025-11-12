<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $vehicle->make }} {{ $vehicle->model }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if($vehicle->year) {{ $vehicle->year }} • @endif
                    {{ ucfirst($vehicle->vendor) }}
                    @if(auth()->user()->is_admin && $vehicle->user_id !== auth()->id())
                        • Owner: {{ $vehicle->user->name }}
                    @endif
                </p>
            </div>
        </div>

        @if($vehicle->user_id === auth()->id())
            <flux:button wire:click="refreshVehicle" :disabled="$isRefreshing" variant="ghost">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh Data
            </flux:button>
        @endif
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Current Status Grid --}}
    <div class="grid gap-6 md:grid-cols-4">
        {{-- Battery --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Battery</span>
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm2 0v8h12V6H4zm1 2h10v4H5V8z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->battery_level ?? 0, 0) }}%</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ $vehicle->charging_status === 'charging' ? '⚡ Charging' : 'Not charging' }}
            </p>
        </div>

        {{-- Odometer --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Odometer</span>
                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->odometer ?? 0, 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $vehicle->odometer_unit ?? 'km' }}</p>
        </div>

        {{-- Range --}}
        @if($vehicle->battery_level && $vehicle->battery_capacity)
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Battery Capacity</span>
                    <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->battery_capacity, 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">kWh</p>
            </div>
        @endif

        {{-- Location --}}
        @if($vehicle->latitude && $vehicle->longitude)
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Location</span>
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <a href="https://www.google.com/maps?q={{ $vehicle->latitude }},{{ $vehicle->longitude }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    View on Map
                </a>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    @if($vehicle->location_updated_at)
                        {{ $vehicle->location_updated_at->diffForHumans() }}
                    @endif
                </p>
            </div>
        @endif
    </div>

    {{-- Statistics Overview (Last 30 Days) --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Statistics (Last 30 Days)</h3>

        <div class="grid gap-6 md:grid-cols-3">
            {{-- Driving Stats --}}
            <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                    </svg>
                    <span class="font-semibold text-blue-900 dark:text-blue-100">Driving</span>
                </div>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $statistics['total_distance_30d'] ?? 0 }} km</p>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                    Efficiency: {{ $statistics['average_efficiency'] ?? 0 }} km/kWh
                </p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                    {{ $statistics['total_energy_used_30d'] ?? 0 }} kWh used
                </p>
            </div>

            {{-- Charging Stats --}}
            <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"/>
                    </svg>
                    <span class="font-semibold text-green-900 dark:text-green-100">Charging</span>
                </div>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $statistics['total_energy_added_30d'] ?? 0 }} kWh</p>
                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                    {{ $statistics['total_charging_sessions_30d'] ?? 0 }} sessions
                </p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                    Avg: {{ $statistics['average_session_duration'] ?? 0 }} min/session
                </p>
            </div>

            {{-- Efficiency --}}
            <div class="p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <span class="font-semibold text-purple-900 dark:text-purple-100">Performance</span>
                </div>
                @php
                    $efficiency = $statistics['average_efficiency'] ?? 0;
                    $energyBalance = ($statistics['total_energy_added_30d'] ?? 0) - ($statistics['total_energy_used_30d'] ?? 0);
                @endphp
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $efficiency }} km/kWh</p>
                <p class="text-sm text-purple-700 dark:text-purple-300 mt-1">
                    Energy balance: {{ number_format($energyBalance, 1) }} kWh
                </p>
                <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                    @if($energyBalance > 0)
                        ↑ Net gain
                    @elseif($energyBalance < 0)
                        ↓ Net used
                    @else
                        = Balanced
                    @endif
                </p>
            </div>
        </div>

        {{-- Monthly Breakdown Chart --}}
        @if(count($statistics['monthly_data'] ?? []) > 0)
            <div class="mt-6 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Monthly Activity</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-neutral-200 dark:border-neutral-700">
                                <th class="text-left py-2 text-gray-600 dark:text-gray-400 font-medium">Month</th>
                                <th class="text-right py-2 text-gray-600 dark:text-gray-400 font-medium">Distance</th>
                                <th class="text-right py-2 text-gray-600 dark:text-gray-400 font-medium">Drives</th>
                                <th class="text-right py-2 text-gray-600 dark:text-gray-400 font-medium">Energy Used</th>
                                <th class="text-right py-2 text-gray-600 dark:text-gray-400 font-medium">Energy Added</th>
                                <th class="text-right py-2 text-gray-600 dark:text-gray-400 font-medium">Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics['monthly_data'] as $monthData)
                                <tr class="border-b border-neutral-100 dark:border-neutral-800 last:border-0">
                                    <td class="py-3 text-gray-900 dark:text-white font-medium">{{ $monthData['month'] }}</td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">{{ number_format($monthData['distance'], 0) }} km</td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">{{ $monthData['drives'] }}</td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">{{ number_format($monthData['energy_used'], 1) }} kWh</td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">{{ number_format($monthData['energy_added'], 1) }} kWh</td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">{{ $monthData['charging_sessions'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Current Location Map --}}
    @if($vehicle->latitude && $vehicle->longitude)
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Current Location</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if($vehicle->location_updated_at)
                        Updated {{ $vehicle->location_updated_at->diffForHumans() }}
                    @endif
                </p>
            </div>
            <div id="location-map" style="height: 300px; border-radius: 0.5rem;" class="overflow-hidden"></div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    window.initLocationMap('location-map', {{ $vehicle->latitude }}, {{ $vehicle->longitude }});
                });
            </script>
        </div>
    @endif

    {{-- Charging Sessions & Drives --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Charging Sessions --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
            <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Charging Sessions</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Last 5 charging sessions</p>
            </div>
            <div class="p-6">
                @if(count($chargingSessions) > 0)
                    <div class="space-y-4">
                        @foreach($chargingSessions as $session)
                            <div class="border-b border-neutral-200 dark:border-neutral-700 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">⚡</span>
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $session->started_at->format('M d, Y') }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $session->started_at->format('g:i A') }}
                                                    @if($session->ended_at)
                                                        - {{ $session->ended_at->format('g:i A') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @if($session->location)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-10">
                                                📍 {{ $session->location }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if($session->battery_gain)
                                            <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                                +{{ number_format($session->battery_gain, 0) }}%
                                            </p>
                                        @endif
                                        @if($session->energy_added)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ number_format($session->energy_added, 1) }} kWh
                                            </p>
                                        @endif
                                        @if($session->duration_minutes)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ floor($session->duration_minutes / 60) }}h {{ $session->duration_minutes % 60 }}m
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400">No charging sessions recorded yet</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Charging sessions will appear here automatically</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Drives --}}
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">
            <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Drives</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Last 5 drives</p>
            </div>
            <div class="p-6">
                @if(count($drives) > 0)
                    <div class="space-y-4">
                        @foreach($drives as $index => $drive)
                            <div class="border-b border-neutral-200 dark:border-neutral-700 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl">🚗</span>
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $drive->started_at->format('M d, Y') }}
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $drive->started_at->format('g:i A') }}
                                                    @if($drive->ended_at)
                                                        - {{ $drive->ended_at->format('g:i A') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($drive->distance)
                                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                                {{ number_format($drive->distance, 1) }} {{ $drive->distance_unit }}
                                            </p>
                                        @endif
                                        @if($drive->battery_used)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                -{{ number_format($drive->battery_used, 0) }}% battery
                                            </p>
                                        @endif
                                        @if($drive->efficiency)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ number_format($drive->efficiency, 1) }} km/kWh
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                @if($drive->start_latitude && $drive->start_longitude && $drive->end_latitude && $drive->end_longitude)
                                    <div id="drive-map-{{ $index }}" style="height: 200px; border-radius: 0.5rem;" class="overflow-hidden"></div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            window.initDriveMap(
                                                'drive-map-{{ $index }}',
                                                {{ $drive->start_latitude }},
                                                {{ $drive->start_longitude }},
                                                {{ $drive->end_latitude }},
                                                {{ $drive->end_longitude }}
                                            );
                                        });
                                    </script>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400">No drives recorded yet</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Drives will be tracked automatically</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Last Update Info --}}
    @if($vehicle->data_updated_at)
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            Last synced {{ $vehicle->data_updated_at->diffForHumans() }}
        </div>
    @endif
</div>
