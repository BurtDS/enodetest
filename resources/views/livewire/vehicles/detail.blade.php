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
            @if($vehicle->previous_battery_level && $vehicle->previous_battery_level != $vehicle->battery_level)
                @php
                    $batteryDiff = $vehicle->battery_level - $vehicle->previous_battery_level;
                @endphp
                <p class="text-xs {{ $batteryDiff > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                    {{ $batteryDiff > 0 ? '+' : '' }}{{ number_format($batteryDiff, 1) }}% from last update
                </p>
            @endif
        </div>

        {{-- Range --}}
        @if($vehicle->range !== null)
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Range</span>
                    <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->range, 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $vehicle->range_unit }}</p>
                @if($vehicle->previous_range && $vehicle->previous_range != $vehicle->range)
                    @php
                        $rangeDiff = $vehicle->range - $vehicle->previous_range;
                    @endphp
                    <p class="text-xs {{ $rangeDiff > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                        {{ $rangeDiff > 0 ? '+' : '' }}{{ number_format($rangeDiff, 0) }} {{ $vehicle->range_unit }} from last update
                    </p>
                @endif
            </div>
        @endif

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
            @if($vehicle->previous_odometer && $vehicle->previous_odometer < $vehicle->odometer)
                @php
                    $odometerDiff = $vehicle->odometer - $vehicle->previous_odometer;
                @endphp
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                    +{{ number_format($odometerDiff, 0) }} {{ $vehicle->odometer_unit ?? 'km' }} from last update
                </p>
            @endif
        </div>

        {{-- Battery Capacity --}}
        @if($vehicle->battery_level && $vehicle->battery_capacity)
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Battery Capacity</span>
                    <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($vehicle->battery_capacity, 0) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">kWh</p>
            </div>
        @endif
    </div>

    {{-- Charging Details --}}
    @if($vehicle->charging_status === 'charging' || $vehicle->is_plugged_in)
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden">
            <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11 3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V5a1 1 0 10-2 0v3.5a.5.5 0 01-1 0V3a1 1 0 10-2 0v8a7 7 0 1014 0V3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V8a1 1 0 10-2 0v.5a.5.5 0 01-1 0V3z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Charging Details</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</span>
                        <div class="flex items-center gap-2">
                            @if($vehicle->charging_status === 'charging')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                    Charging
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                    Not Charging
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($vehicle->is_plugged_in !== null)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Plugged In</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $vehicle->is_plugged_in ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    @endif

                    @if($vehicle->charge_rate)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Charge Rate</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ number_format($vehicle->charge_rate, 1) }} kW</span>
                        </div>
                    @endif

                    @if($vehicle->charge_time_remaining)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Time Remaining</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">
                                @php
                                    $hours = floor($vehicle->charge_time_remaining / 60);
                                    $minutes = $vehicle->charge_time_remaining % 60;
                                @endphp
                                {{ $hours > 0 ? $hours . 'h ' : '' }}{{ $minutes }}m
                            </span>
                        </div>
                    @endif

                    @if($vehicle->is_fully_charged !== null)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Fully Charged</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $vehicle->is_fully_charged ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    @endif

                    @if($vehicle->charge_limit)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Charge Limit</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $vehicle->charge_limit }}%</span>
                        </div>
                    @endif

                    @if($vehicle->power_delivery_state)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Power Delivery State</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ ucfirst($vehicle->power_delivery_state) }}</span>
                        </div>
                    @endif

                    @if($vehicle->max_current)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Max Current</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $vehicle->max_current }} A</span>
                        </div>
                    @endif

                    @if($vehicle->plugged_in_charger_id)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Charger ID</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $vehicle->plugged_in_charger_id }}</span>
                        </div>
                    @endif

                    @if($vehicle->charge_state_updated_at)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Charge State Updated</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $vehicle->charge_state_updated_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Smart Charging --}}
    @if($vehicle->smart_charging_enabled)
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden">
            <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11 3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V5a1 1 0 10-2 0v3.5a.5.5 0 01-1 0V3a1 1 0 10-2 0v8a7 7 0 1014 0V3a1 1 0 10-2 0v5.5a.5.5 0 01-1 0V8a1 1 0 10-2 0v.5a.5.5 0 01-1 0V3z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Smart Charging</h3>
                    <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        Active
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid gap-4 md:grid-cols-3">
                    @if($vehicle->smart_charging_deadline)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Deadline</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $vehicle->smart_charging_deadline->format('M d, Y g:i A') }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $vehicle->smart_charging_deadline->diffForHumans() }}
                            </span>
                        </div>
                    @endif

                    @if($vehicle->smart_charging_minimum_charge_limit)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Minimum Charge Limit</span>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $vehicle->smart_charging_minimum_charge_limit }}%</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Vehicle Status & Connectivity --}}
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden">
        <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Vehicle Status</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="grid gap-4 md:grid-cols-3">
                @if($vehicle->is_reachable !== null)
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Reachable</span>
                        <div class="flex items-center gap-2">
                            @if($vehicle->is_reachable)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></span>
                                    Online
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span>
                                    Offline
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                @if($vehicle->last_seen)
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Last Seen</span>
                        <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $vehicle->last_seen->diffForHumans() }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $vehicle->last_seen->format('M d, Y g:i A') }}</span>
                    </div>
                @endif

                @if($vehicle->display_name)
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Display Name</span>
                        <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $vehicle->display_name }}</span>
                    </div>
                @endif

                @if($vehicle->vin)
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">VIN</span>
                        <span class="text-base font-mono text-gray-900 dark:text-white">{{ $vehicle->vin }}</span>
                    </div>
                @endif

                @if($vehicle->latitude && $vehicle->longitude)
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">Location</span>
                        <a href="https://www.google.com/maps?q={{ $vehicle->latitude }},{{ $vehicle->longitude }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            View on Map
                        </a>
                        @if($vehicle->location_updated_at)
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Updated {{ $vehicle->location_updated_at->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Capabilities --}}
    @if($vehicle->capabilities && count($vehicle->capabilities) > 0)
        <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden">
            <div class="p-6 border-b border-neutral-200 dark:border-neutral-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Vehicle Capabilities</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-2">
                    @foreach($vehicle->capabilities as $capability)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ ucfirst(str_replace('_', ' ', $capability)) }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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
