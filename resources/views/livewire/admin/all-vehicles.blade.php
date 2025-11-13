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
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Vehicle</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Owner</th>
                        <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">State of Charge</th>
                        <th class="text-right py-4 px-6 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Odometer</th>
                        <th class="text-center py-4 px-6 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-center py-4 px-6 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Last Updated</th>
                        <th class="text-center py-4 px-6 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-900/50 transition-colors">
                            {{-- Vehicle Info --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $vehicle->make }} {{ $vehicle->model }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($vehicle->year)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $vehicle->year }}</span>
                                            @endif
                                            @if($vehicle->vendor)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ ucfirst($vehicle->vendor) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Owner --}}
                            <td class="py-4 px-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $vehicle->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $vehicle->user->email }}</p>
                                </div>
                            </td>

                            {{-- Battery Level with Visualization --}}
                            <td class="py-4 px-6">
                                @if($vehicle->battery_level !== null)
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 min-w-[120px]">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Battery</span>
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->battery_level, 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700 overflow-hidden">
                                                @php
                                                    $batteryColor = match(true) {
                                                        $vehicle->battery_level >= 80 => 'bg-green-500',
                                                        $vehicle->battery_level >= 50 => 'bg-blue-500',
                                                        $vehicle->battery_level >= 20 => 'bg-yellow-500',
                                                        default => 'bg-red-500'
                                                    };
                                                @endphp
                                                <div class="{{ $batteryColor }} h-2 rounded-full transition-all duration-300" style="width: {{ $vehicle->battery_level }}%"></div>
                                            </div>
                                        </div>
                                        @if($vehicle->charging_status === 'charging')
                                            <div class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M11 3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V5a1 1 0 10-2 0v3.5a.5.5 0 01-1 0V3a1 1 0 10-2 0v8a7 7 0 1014 0V3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V8a1 1 0 10-2 0v.5a.5.5 0 01-1 0V3z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">N/A</span>
                                @endif
                            </td>

                            {{-- Odometer with Visualization --}}
                            <td class="py-4 px-6">
                                @if($vehicle->odometer !== null)
                                    <div class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12z"/>
                                            </svg>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ number_format($vehicle->odometer, 0) }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $vehicle->odometer_unit ?? 'km' }}</span>
                                        @if($vehicle->previous_odometer && $vehicle->previous_odometer < $vehicle->odometer)
                                            @php
                                                $odometerDiff = $vehicle->odometer - $vehicle->previous_odometer;
                                            @endphp
                                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                +{{ number_format($odometerDiff, 0) }} {{ $vehicle->odometer_unit ?? 'km' }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">N/A</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-6">
                                <div class="flex justify-center">
                                    @if($vehicle->charging_status === 'charging')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                            Charging
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-1.5"></span>
                                            Idle
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Last Updated --}}
                            <td class="py-4 px-6 text-center">
                                @if($vehicle->data_updated_at)
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        <div>{{ $vehicle->data_updated_at->format('M d, Y') }}</div>
                                        <div class="text-gray-500 dark:text-gray-500">{{ $vehicle->data_updated_at->format('g:i A') }}</div>
                                        <div class="text-gray-500 dark:text-gray-500 mt-1">
                                            ({{ $vehicle->data_updated_at->diffForHumans() }})
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">Never</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-6">
                                <div class="flex justify-center">
                                    <a href="{{ route('vehicles.detail', $vehicle) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
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
</div>
