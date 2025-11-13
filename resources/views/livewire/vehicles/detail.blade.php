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
            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700 mt-2">
                <div class="h-2 rounded-full {{ ($vehicle->battery_level ?? 0) > 20 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $vehicle->battery_level ?? 0 }}%"></div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
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

        {{-- Battery Capacity --}}
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

    {{-- Monthly Statistics --}}
    @if(count($statistics['monthly_data'] ?? []) > 0)
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Monthly Overview</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Energy consumed and kilometers driven per month</p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="text-left py-3 text-gray-600 dark:text-gray-400 font-medium">Month</th>
                            <th class="text-right py-3 text-gray-600 dark:text-gray-400 font-medium">Distance (km)</th>
                            <th class="text-right py-3 text-gray-600 dark:text-gray-400 font-medium">Energy Used (kWh)</th>
                            <th class="text-right py-3 text-gray-600 dark:text-gray-400 font-medium">Efficiency (km/kWh)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statistics['monthly_data'] as $monthData)
                            <tr class="border-b border-neutral-100 dark:border-neutral-800 last:border-0">
                                <td class="py-3 text-gray-900 dark:text-white font-medium">{{ $monthData['month'] }}</td>
                                <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                    {{ number_format($monthData['distance'], 0) }}
                                </td>
                                <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                    {{ number_format($monthData['energy_used'], 1) }}
                                </td>
                                <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                    @php
                                        $efficiency = $monthData['energy_used'] > 0 ? $monthData['distance'] / $monthData['energy_used'] : 0;
                                    @endphp
                                    {{ number_format($efficiency, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

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

    {{-- Last Update Info --}}
    @if($vehicle->data_updated_at)
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            Last synced {{ $vehicle->data_updated_at->diffForHumans() }}
        </div>
    @endif
</div>
