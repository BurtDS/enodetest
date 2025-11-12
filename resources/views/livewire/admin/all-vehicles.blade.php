<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Overview of all colleagues and their vehicles</p>
        </div>
    </div>

    {{-- User Statistics --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Team Statistics (Last 30 Days)</h3>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-neutral-200 dark:border-neutral-700">
                        <th class="text-left py-3 text-gray-600 dark:text-gray-400 font-medium">User</th>
                        <th class="text-right py-3 text-gray-600 dark:text-gray-400 font-medium">Vehicles</th>
                        <th class="text-right py-3 text-gray-600 dark:text-gray-400 font-medium">Distance</th>
                        <th class="text-right py-3 text-gray-600 dark:text-gray-400 font-medium">Energy Used</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $userData)
                        <tr class="border-b border-neutral-100 dark:border-neutral-800 last:border-0">
                            <td class="py-4">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $userData['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $userData['email'] }}</p>
                                </div>
                            </td>
                            <td class="py-4 text-right text-gray-700 dark:text-gray-300">
                                {{ $userData['vehicles_count'] }}
                            </td>
                            <td class="py-4 text-right text-gray-700 dark:text-gray-300">
                                {{ number_format($userData['total_distance_30d'], 0) }} km
                            </td>
                            <td class="py-4 text-right text-gray-700 dark:text-gray-300">
                                {{ number_format($userData['total_energy_30d'], 1) }} kWh
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- All Vehicles --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">All Vehicles</h3>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($vehicles as $vehicle)
                <a href="{{ route('vehicles.detail', $vehicle) }}" wire:navigate class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden hover:border-neutral-300 dark:hover:border-neutral-600 transition-colors block">
                    {{-- Vehicle Header --}}
                    <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-900">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-md font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $vehicle->make }} {{ $vehicle->model }}
                                </h3>
                                @if($vehicle->year)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $vehicle->year }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ ucfirst($vehicle->vendor) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Owner: {{ $vehicle->user->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Data --}}
                    <div class="p-4 space-y-3">
                        {{-- Battery Status --}}
                        @if($vehicle->battery_level !== null)
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Battery</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->battery_level, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                    <div class="h-2 rounded-full {{ $vehicle->battery_level > 20 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $vehicle->battery_level }}%"></div>
                                </div>
                            </div>
                        @endif

                        {{-- Odometer --}}
                        @if($vehicle->odometer !== null)
                            <div class="flex items-center justify-between py-2 border-t border-neutral-200 dark:border-neutral-700">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Odometer</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ number_format($vehicle->odometer, 0) }} {{ $vehicle->odometer_unit ?? 'km' }}
                                </span>
                            </div>
                        @endif

                        {{-- Location Map --}}
                        @if($vehicle->latitude && $vehicle->longitude)
                            <div class="border-t border-neutral-200 dark:border-neutral-700 pt-3">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">Location</span>
                                <div id="admin-map-{{ $vehicle->id }}" style="height: 120px; border-radius: 0.5rem;" class="overflow-hidden"></div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        window.initLocationMap('admin-map-{{ $vehicle->id }}', {{ $vehicle->latitude }}, {{ $vehicle->longitude }});
                                    });
                                </script>
                                @if($vehicle->location_updated_at)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Updated {{ $vehicle->location_updated_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Last Update --}}
                        @if($vehicle->data_updated_at)
                            <div class="pt-2 border-t border-neutral-200 dark:border-neutral-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Last synced {{ $vehicle->data_updated_at->diffForHumans() }}
                                </p>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
